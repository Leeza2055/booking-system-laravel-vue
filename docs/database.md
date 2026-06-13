# Database Schema

## Entity Relationships
```
users (role: customer / provider / admin)
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

## Tables

### users
Standard Laravel users table extended with one column.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `role` | string | `customer` | Three fixed roles: `customer`, `provider`, `admin`. String (not enum) for simplicity. Values documented in comment on the migration. |

---

### providers
A professional who offers bookable services. Always linked to a user account — the user handles auth, the provider record holds professional context.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `user_id` | FK → users | — | Links professional identity to a login. Cascade: same entity. |
| `department` | string | — | Plain string for now — e.g. "Cardiology", "Mathematics". See LATER.md for extraction plan. |
| `specialization` | string | — | More specific than department — distinguishes two providers in the same department. |
| `bio` | text | null | Nullable — can be added after creation. Shown on listing page. |
| `base_fee` | decimal(8,2) | — | **Display hint only** — shown as "from Rs. X" on listings. Never used in transactions. See `services.price`. |
| `is_active` | boolean | true | Hides provider from listings without deleting booking history. |

---

### services
What a provider offers. `price` here is the **actual transaction amount** — what gets passed to Khalti.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `provider_id` | FK → providers | — | Cascade: services are meaningless without their provider. |
| `name` | string | — | e.g. "General Consultation", "Follow-up Visit", "A-Level Maths". |
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
| `day_of_week` | tinyint unsigned | — | 0=Sunday … 6=Saturday. Integer for easy comparison and ordering. |
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
| `provider_id` | FK → providers | — | References `providers`, not `users` — a booking belongs to the provider record (owns schedule/pricing), not the raw user. |
| `service_id` | FK → services | — | Records which service was booked. Used for `end_time` calculation and receipt display. |
| `customer_id` | FK → users | — | Named `customer_id` not `user_id` — explicit about who this is. Uses `constrained('users')` because the column name differs from the default. |
| `booking_date` | date | — | Named `booking_date` not `date` — `date` is a reserved word in MySQL. Avoids a subtle dialect bug. |
| `start_time` | time | — | Slot start chosen by the customer. |
| `end_time` | time | — | Stored explicitly (not derived) so historical records stay accurate if service duration changes later. |
| `status` | string | `pending` | Workflow: `pending` → `confirmed` → `completed` / `cancelled`. |
| `notes` | text | null | Optional customer notes (symptoms, requests). |
| `deleted_at` | timestamp | null | Added by `softDeletes()`. Cancelled/removed bookings get a timestamp here — disappear from queries but never leave the database. |

**Unique index:** `(provider_id, booking_date, start_time)` — database-level double-booking prevention. Application logic checks first; this is the safety net. A race condition cannot produce a duplicate booking.

**Performance index:** `(customer_id, booking_date)` — for customer booking history queries. Without it, listing a customer's bookings is a full table scan.

**Status workflow:**
```
pending → confirmed → completed
        ↘ cancelled
```

---

### payments
One record per booking. Never deleted — financial audit trail.

| Column | Type | Default | Why |
|--------|------|---------|-----|
| `booking_id` | FK → bookings | — | Restrict: a booking cannot be removed while a payment record references it. |
| `amount` | decimal(8,2) | — | **Copied from `services.price` at booking time.** Intentional denormalization — records what was actually charged, regardless of future price changes. |
| `payment_method` | string | `khalti` | Named `payment_method` not `method` — too generic, could be confused with HTTP method. Allows future methods (eSewa, cash) to be recorded cleanly. |
| `transaction_id` | string | null | Khalti's transaction ID. Nullable at creation (set after Khalti responds). Unique — prevents the same transaction being recorded twice. |
| `status` | string | `pending` | Khalti lifecycle: `pending` → `completed` / `failed`. Separate from `bookings.status`. |
| `paid_at` | timestamp | null | Set on Khalti confirmation. Null = payment not yet completed. |

> **Note:** `amount` is deliberately denormalized. Financial records must reflect what was charged at the time of the transaction, not what the service costs today.

---

## Eloquent Relationships

> **Add here as you define them in the model files.**

```php
// User
hasOne(Provider::class)
hasMany(Booking::class, 'customer_id')

// Provider
belongsTo(User::class)
hasMany(Service::class)
hasMany(Schedule::class)
hasMany(Booking::class)

// Service
belongsTo(Provider::class)
hasMany(Booking::class)

// Schedule
belongsTo(Provider::class)

// Booking
belongsTo(Provider::class)
belongsTo(Service::class)
belongsTo(User::class, 'customer_id')   // custom FK
hasOne(Payment::class)

// Payment
belongsTo(Booking::class)
```

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
