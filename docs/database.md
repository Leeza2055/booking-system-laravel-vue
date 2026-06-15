# Database Schema

## Entity Relationships
```
users (role: Customer / Provider / Admin — UserRole enum)
  └── providers          (one user → one provider profile)
        ├── services      (provider offers many services)
        ├── schedules     (provider sets weekly availability)
        └── bookings      (provider receives many bookings)
              ├── linked to a service
              ├── linked to a customer (user)
              └── payments (one booking → one payment)
```

## Delete Chain
| Relationship | Behaviour | Reason |
|---|---|---|
| user → provider | cascade | A provider profile is meaningless without the user |
| provider → services | cascade | Services are meaningless without their provider |
| provider → schedules | cascade | Schedules are meaningless without their provider |
| provider → bookings | **restrict** | Booking history must survive provider departure |
| service → bookings | **restrict** | Bookings must retain their service reference |
| user (customer) → bookings | **restrict** | Booking history must survive customer account changes |
| booking → payments | **restrict** | Financial audit trail must never be destroyed |

---

## Enums
Status, role, and day-of-week fields use PHP backed enums (`App\Enums`) rather than raw strings/ints, cast via each model's `casts()` method.

| Enum | Backing type | Cases | Used on |
|------|-------------|-------|---------|
| `UserRole` | string | `Admin`, `Customer`, `Provider` | `users.role` |
| `BookingStatus` | string | `PENDING`, `CONFIRMED`, `COMPLETED`, `CANCELLED` | `bookings.status` |
| `PaymentStatus` | string | `PENDING`, `COMPLETED`, `FAILED` | `payments.status` |
| `DayOfWeek` | int | `SUNDAY`(0) … `SATURDAY`(6) | `schedules.day_of_week` |

> **Note:** `UserRole` cases are title-case (`Admin`, `Customer`, `Provider`); the other three enums use ALL_CAPS (`PENDING`, etc.). Inconsistent, but both are in active use across seeders/factories — left as-is rather than risk breaking working code. New enums should use ALL_CAPS.

`DayOfWeek` values (0=Sunday … 6=Saturday) match Carbon's `->dayOfWeek` output, so the slot generator (Week 3) can convert a date directly: `DayOfWeek::from(Carbon::parse($date)->dayOfWeek)`.

---

## Tables

### users
Standard Laravel users table extended with one column.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `role` | string, cast to `UserRole` | `customer` | Three fixed roles: `Admin`, `Customer`, `Provider`. Enum cast for type safety — see Enums section. |

---

### providers
A professional who offers bookable services. Always linked to a user account — the user handles auth, the provider record holds professional context.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `user_id` | FK → users | — | Links professional identity to a login. Cascade: same entity. |
| `department` | string | — | Plain string for now — e.g. "Cardiology". See LATER.md for extraction plan. |
| `specialization` | string | — | More specific than department — distinguishes two providers in the same department. Paired with department in `ProviderFactory` (e.g. Cardiology → Cardiologist) so seeded data stays realistic. |
| `bio` | text | null | Nullable — can be added after creation. Shown on listing page. |
| `base_fee` | decimal(8,2) | — | **Display hint only** — shown as "from Rs. X" on listings. Never used in transactions. See `services.price`. |
| `is_active` | boolean | true | Hides provider from listings without deleting booking history. |

---

### services
What a provider offers. `price` here is the **actual transaction amount** — what gets passed to Khalti.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `provider_id` | FK → providers | — | Cascade: services are meaningless without their provider. |
| `name` | string | — | e.g. "General Consultation", "ECG", "Dental Checkup". `ServiceFactory` picks a name matching the provider's specialization via `afterMaking` (e.g. a Cardiologist gets "ECG", "Cardiac Consultation"). |
| `description` | text | null | Nullable — name alone is often sufficient at creation. |
| `duration_minutes` | smallint unsigned | — | Named `duration_minutes` not `duration` — unit is self-documenting. Used by slot generator to calculate `end_time`. |
| `price` | decimal(8,2) | — | **Transaction amount.** Always use this for charging and Khalti. Not `providers.base_fee`. |
| `is_active` | boolean | true | Retire a service without deleting past bookings that reference it. |

> **Note:** `providers.base_fee` is a display hint. `services.price` is the financial truth. Never mix them.

---

### schedules
Provider's recurring weekly availability. One row per working-day pattern. Input to `SlotGeneratorService`.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `provider_id` | FK → providers | — | Cascade: schedule rows are meaningless without their provider. |
| `day_of_week` | tinyint unsigned, cast to `DayOfWeek` | — | 0=Sunday … 6=Saturday via `DayOfWeek` enum. Integer column for easy comparison/ordering; enum for readability in code. |
| `start_time` | time | — | When the working day begins on this day. |
| `end_time` | time | — | When it ends. Slot generator fills this window. |
| `slot_duration_minutes` | smallint unsigned | 30 | Slot length. Default 30 covers most professional contexts. |
| `is_active` | boolean | true | Pause a day temporarily without deleting the row. |

**Unique index:** `(provider_id, day_of_week, start_time)` — prevents duplicate schedule entries for the same provider/day/time. Without this, the slot generator could produce duplicate available slots.

---

### bookings
Core transaction record. Soft-deleted — never hard-deleted.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `provider_id` | FK → providers, `restrictOnDelete` | — | References `providers`, not `users` — a booking belongs to the provider record (owns schedule/pricing), not the raw user. |
| `service_id` | FK → services, `restrictOnDelete` | — | Records which service was booked. Used for `end_time` calculation and receipt display. |
| `customer_id` | FK → users, `restrictOnDelete` | — | Named `customer_id` not `user_id` — explicit about who this is. Uses `constrained('users')` because the column name differs from the default. |
| `booking_date` | date | — | Named `booking_date` not `date` — `date` is a reserved word in MySQL. Avoids a subtle dialect bug. |
| `start_time` | time | — | Slot start chosen by the customer. |
| `end_time` | time | — | Stored explicitly (not derived) so historical records stay accurate if service duration changes later. |
| `status` | string, cast to `BookingStatus` | `pending` | Workflow: `PENDING` → `CONFIRMED` → `COMPLETED` / `CANCELLED`. |
| `notes` | text | null | Optional customer notes (symptoms, requests). |
| `deleted_at` | timestamp | null | Added by `softDeletes()`. Cancelled/removed bookings get a timestamp here — disappear from queries but never leave the database. |

**Unique index:** `(provider_id, booking_date, start_time)` — database-level double-booking prevention. Application logic checks first; this is the safety net. A race condition cannot produce a duplicate booking.

**Performance index:** `(customer_id, booking_date)` — for customer booking history queries. Without it, listing a customer's bookings is a full table scan.

**Status workflow:**
```
PENDING → CONFIRMED → COMPLETED
        ↘ CANCELLED
```

---

### payments
One record per booking. Never deleted — financial audit trail.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `booking_id` | FK → bookings, `restrictOnDelete` | — | A booking cannot be removed while a payment record references it. |
| `amount` | decimal(8,2) | — | **Copied from `services.price` at booking time.** Intentional denormalization — records what was actually charged, regardless of future price changes. |
| `payment_method` | string | `khalti` | Named `payment_method` not `method` — too generic. Allows future methods (eSewa, cash) to be recorded cleanly. |
| `transaction_id` | string | null | Khalti's transaction ID. Nullable at creation (set after Khalti responds). Unique — prevents the same transaction being recorded twice. |
| `status` | string, cast to `PaymentStatus` | `pending` | Khalti lifecycle: `PENDING` → `COMPLETED` / `FAILED`. Separate from `bookings.status`. |
| `paid_at` | timestamp | null | Set on Khalti confirmation. Null = payment not yet completed. |

> **Note:** `amount` is deliberately denormalized. Financial records must reflect what was charged at the time of the transaction, not what the service costs today.

---

## Eloquent Relationships
All relationship methods have explicit return types with generic parameters (`<RelatedModel, $this>`), required by Larastan at level 7.

```php
// User
/** @return HasOne<Provider, $this> */
public function provider(): HasOne

/** @return HasMany<Booking, $this> */
public function bookings(): HasMany   // via customer_id

// Provider
/** @return BelongsTo<User, $this> */
public function user(): BelongsTo

/** @return HasMany<Service, $this> */
public function services(): HasMany

/** @return HasMany<Schedule, $this> */
public function schedules(): HasMany

/** @return HasMany<Booking, $this> */
public function bookings(): HasMany

// Service
/** @return BelongsTo<Provider, $this> */
public function provider(): BelongsTo

/** @return HasMany<Booking, $this> */
public function bookings(): HasMany

// Schedule
/** @return BelongsTo<Provider, $this> */
public function provider(): BelongsTo

// Booking
/** @return BelongsTo<Provider, $this> */
public function provider(): BelongsTo

/** @return BelongsTo<Service, $this> */
public function service(): BelongsTo

/** @return BelongsTo<User, $this> */
public function customer(): BelongsTo   // custom FK: customer_id

/** @return HasOne<Payment, $this> */
public function payment(): HasOne

// Payment
/** @return BelongsTo<Booking, $this> */
public function booking(): BelongsTo
```

All models use `HasFactory` with a `@use HasFactory<XFactory>` doc comment for Larastan, e.g.:
```php
/** @use HasFactory<ProviderFactory> */
use HasFactory;
```

---

## Seeders & Factories
Run via `php artisan migrate:fresh --seed`. Order matters — each seeder depends on data from the previous one.

| Seeder | Creates | Depends on |
|--------|---------|-----------|
| `UserSeeder` | 2 known accounts: `admin@example.com` and `customer@example.com` (password: `password`, email pre-verified) — for manual login testing | — |
| `ProviderSeeder` | 10 providers via `Provider::factory(10)`, each with its own auto-generated provider-role user | `UserFactory` (nested) |
| `ServiceSeeder` | 2-4 services per existing provider, name matched to specialization via `afterMaking` | `ProviderSeeder` |
| `ScheduleSeeder` | 5 schedule rows per provider (Mon-Fri), realistic shift times via `ScheduleFactory` | `ProviderSeeder` |

**Factory notes:**
- `ProviderFactory` pairs department/specialization from a fixed map (e.g. Cardiology → Cardiologist) so data stays internally consistent.
- `ServiceFactory` uses `configure()` + `afterMaking` to read the related provider's `specialization` and pick a matching service name from `$servicesBySpecialization`.
- `ScheduleFactory` picks one of four realistic shift patterns (e.g. 09:00-17:00) as a pair, so `start_time` is always before `end_time`. `ScheduleSeeder` overrides `day_of_week` per weekday to guarantee Mon-Fri coverage without duplicates (unique index safe).

---

## Migration Order
Migrations must run in this order (foreign key dependencies):
1. users (framework default)
2. add_role_to_users
3. providers (needs users)
4. services (needs providers)
5. schedules (needs providers)
6. bookings (needs providers, services, users)
7. payments (needs bookings)

Laravel uses migration file timestamps to order execution — ensure your filenames reflect this sequence.
