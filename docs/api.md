# API Endpoints

## Auth
Handled by Laravel Fortify. Key routes:
- `POST /login` — `login.store`
- `POST /logout` — `logout`
- `POST /register` — `register.store`
- `GET /dashboard` — `dashboard` (auth + verified)

---

## Providers (Public)
| Method | URL | Name | Description |
|--------|-----|------|-------------|
| GET | `/providers` | `providers.index` | List active providers |
| GET | `/providers/{provider}` | `providers.show` | Provider detail with services |

---

## Providers (Admin)
Requires: `auth`, `verified`, `admin` middleware

| Method | URL | Name | Description |
|--------|-----|------|-------------|
| GET | `/admin/providers` | `admin.providers.index` | List all providers |
| GET | `/admin/providers/create` | `admin.providers.create` | Create form |
| POST | `/admin/providers` | `admin.providers.store` | Create provider + user account |
| GET | `/admin/providers/{provider}/edit` | `admin.providers.edit` | Edit form |
| PUT | `/admin/providers/{provider}` | `admin.providers.update` | Update provider |

> `show` and `destroy` excluded — admin goes directly to edit, delete deferred to LATER.md.

---

## Services (Provider)
Requires: `auth`, `verified`, `provider` middleware
Scoped to logged-in provider — cannot access other providers' services.

| Method | URL | Name | Description |
|--------|-----|------|-------------|
| GET | `/provider/services` | `provider.services.index` | List own services |
| GET | `/provider/services/create` | `provider.services.create` | Create form |
| POST | `/provider/services` | `provider.services.store` | Create service |
| GET | `/provider/services/{service}/edit` | `provider.services.edit` | Edit form |
| PUT | `/provider/services/{service}` | `provider.services.update` | Update service |
| DELETE | `/provider/services/{service}` | `provider.services.destroy` | Delete service |

> Ownership enforced in `edit` and `update` — returns 403 if service doesn't belong to logged-in provider.

---

## Schedules (Provider)
> Add after schedule management is built.

## Bookings
> Add after booking engine is built (Week 3-4).

## Payments
> Add after Khalti integration is built (Week 5).
