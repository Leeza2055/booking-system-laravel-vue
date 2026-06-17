# Architecture

## Overview
Generic person-based booking engine. The domain (clinic) is a skin applied at the UI layer only — models, columns, and routes use neutral names (`Provider`, `Service`, `customer_id`) so the engine can be reskinned for any professional-to-customer booking scenario without touching the database.

**Key boundary:** providers are *people* linked to user accounts. This suits clinics, tutors, consultants, and salons naturally. Resource-based booking (courts, rooms, equipment) would need a different schema — the `user_id` link on providers becomes awkward when a provider is a physical resource rather than a person.

---

## Tech Stack
| Component | Version |
|-----------|---------|
| PHP | ^8.4 |
| Laravel | 13.x |
| Inertia.js | 3.x |
| Vue | 3.x |
| Pest | 4.x |
| Node.js | 22.x |
| CI PHP matrix | 8.4, 8.5 |

---

## Tech Decisions

### Inertia.js + Vue (not a separate API)
Laravel routing and controllers stay the source of truth. Vue components are served as Inertia page responses — no separate API layer, no token auth complexity, no CORS configuration.

**Trade-off:** tighter frontend/backend coupling. A decoupled REST API + Vue SPA would be preferred if a mobile app were in scope.

> **LATER:** if a mobile app is added, extract a Sanctum token-auth API layer alongside the existing Inertia setup.

### Laravel Fortify for authentication
The starter kit ships with Fortify, which handles login, registration, password reset, email verification, and two-factor auth. Role-based redirects after login are configured through Fortify's `authenticated` callback in `FortifyServiceProvider`.

### Role as a string column on users
Simple enum-as-string (`customer` / `provider` / `admin`) on the users table. Three fixed roles don't justify pulling in a full permissions package.

> **LATER:** migrate to Spatie Laravel Permission if roles become granular (provider sub-roles, admin tiers).

### department as a string on providers
Avoids a separate `departments` table for now. Trade-off: string values repeat across providers and can't carry metadata (description, icon, slug).

> **LATER:** extract to a `departments` table with `department_id` FK on providers if a department-browse feature is needed.

### base_fee vs service price
`providers.base_fee` — display hint shown on listing pages ("from Rs. X"). **Never used in financial logic.**
`services.price` — actual amount charged at booking time. **Always use this for transactions and Khalti.**

### Double-booking prevention at two layers
1. **Application layer** — `SlotGeneratorService` checks availability before creating a booking (Week 3).
2. **Database layer** — unique index on `(provider_id, booking_date, start_time)`. Even a race condition cannot produce duplicate bookings; only one INSERT wins the unique constraint.

### amount denormalization on payments
`payments.amount` copies `services.price` at booking time rather than referencing it dynamically. **Intentional denormalization** — financial records must reflect what was charged at the time of transaction, not the current price of a service.

### Soft deletes on bookings
Bookings are never hard-deleted. `softDeletes()` adds `deleted_at`; cancelled bookings get a timestamp and disappear from normal queries but remain in the database, preserving the payment audit trail permanently.

### In-memory SQLite for tests
`phpunit.xml` points tests at an in-memory SQLite database. `RefreshDatabase` is applied globally to Feature tests in `tests/Pest.php`.

**Trade-off:** if any query uses MySQL-specific syntax, tests pass but production fails. Mitigation: use Eloquent only — no raw SQL.

> **LATER:** switch to a dedicated MySQL test database if MySQL-specific features are needed.

### restrictOnDelete on bookings foreign keys
`bookings.provider_id`, `bookings.service_id`, and `bookings.customer_id` all use `restrictOnDelete`. Providers and services cannot be deleted while bookings reference them — protecting booking history. Contrast with `cascadeOnDelete` on providers → services/schedules, where those records are meaningless without the provider.

### Larastan for static analysis
The starter kit ships with Larastan (PHPStan for Laravel). The `composer types:check` command runs on every CI push. All model relationships must have explicit return types to pass analysis.

### CI matrix: PHP 8.4 and 8.5
Both versions confirmed green. PHP 8.3 was removed — `composer.lock` contains Symfony 8.1 packages requiring PHP >=8.4.1. `composer.json` declares `"php": "^8.4"`.

### Ownership enforcement in provider-scoped controllers
Provider controllers scope all queries to `Auth::user()->provider` — a provider can only see and modify their own resources. Ownership is explicitly verified in `edit` and `update` methods with `abort(403)` rather than relying solely on query scoping.

### Role-based middleware
Two custom middleware classes gate access by role. Registered as aliases in `bootstrap/app.php` and applied to route groups:
- `admin` → `EnsureUserIsAdmin` — aborts 403 if `user->role !== UserRole::Admin`
- `provider` → `EnsureUserIsProvider` — aborts 403 if `user->role !== UserRole::Provider`

---

## Folder Structure
```
app/
  Models/
    User.php
    Provider.php
    Service.php
    Schedule.php
    Booking.php
    Payment.php
  Http/
    Controllers/
      ProviderController.php           — public provider listing and detail
      Admin/
        ProviderController.php         — admin provider CRUD
      Provider/
        ServiceController.php          — provider-scoped service CRUD
    Middleware/
      EnsureUserIsAdmin.php            — aborts 403 if role != Admin
      EnsureUserIsProvider.php         — aborts 403 if role != Provider
  Services/
    SlotGeneratorService.php           — slot generation logic (Week 3)
resources/
  js/
    pages/
      providers/
        Index.vue                      — public provider listing
        Show.vue                       — public provider detail with services
      admin/
        providers/
          Index.vue                    — admin provider table
          Create.vue                   — admin create provider + user form
          Edit.vue                     — admin edit provider form
      provider/
        services/
          Index.vue                    — provider service table
          Create.vue                   — provider create service form
          Edit.vue                     — provider edit service form
    routes/                            — Wayfinder generated route functions
    types/
      index.ts                         — shared TypeScript interfaces (Provider, Service)
      auth.ts                          — User, Auth types (canonical User definition)
    components/
      ui/                              — shadcn components
docs/                                  — project documentation
tests/
  Feature/                             — HTTP-level tests
  Unit/                                — unit tests for SlotGeneratorService
.github/workflows/
  tests.yml                            — CI pipeline (PHP 8.4, 8.5)
  lint.yml                             — code style checks
```

> **Add here as you build:** when you create a new controller, middleware, or service class, add it to the folder structure above with a one-line description.

---

## Slot Generation (Week 3 preview)
`SlotGeneratorService` will:
1. Read the provider's `schedules` row for the requested day of week
2. Generate all possible slots between `start_time` and `end_time` using `slot_duration_minutes`
3. Query `bookings` for that provider and date to find already-taken slots
4. Return the difference as available slots

This logic lives in a dedicated service class (not a controller) so it can be unit-tested independently.
