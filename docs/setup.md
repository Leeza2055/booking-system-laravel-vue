# Local Setup Guide

## Prerequisites
- PHP 8.4+ with extensions: `mbstring`, `xml`, `curl`, `sqlite3`, `pdo_mysql`
- Composer v2
- Node.js 22+ and npm
- MySQL 8+
- Git configured with the email linked to your GitHub account (required for contributions to appear on your graph)

## Steps

### 1. Clone and install
```bash
git clone https://github.com/Leeza2055/booking-system-laravel-vue
cd booking-system-laravel-vue
composer install
npm install
```

### 2. Environment
```bash
cp .env.example .env
php artisan key:generate
```
Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```
Create the database:
```sql
CREATE DATABASE booking_system;
```

### 3. Database
```bash
php artisan migrate
php artisan db:seed   # once seeders are added (Week 2)
```

### 4. Run
Two terminals side by side:
```bash
# Terminal 1 — Vite hot reload
npm run dev

# Terminal 2 — Laravel dev server
php artisan serve
```
Visit http://localhost:8000

### 5. Verify tests pass
```bash
php artisan test
```
All tests should be green. Tests use in-memory SQLite — no separate test database needed.

---

## CI Notes
- Runs on push/PR to `develop` and `main`
- PHP matrix: `8.4` and `8.5` (both confirmed green)
- PHP 8.3 removed — `composer.lock` has Symfony 8.1 packages requiring PHP >= 8.4.1
- `composer.json` declares `"php": "^8.4"`
- `RefreshDatabase` enabled globally for Feature tests in `tests/Pest.php`

---

## Common Issues

**"No such table: users" in tests**
`RefreshDatabase` must be active. Check `tests/Pest.php`:
```php
pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');
```

**CI fails with PHP version error**
Check `.github/workflows/tests.yml` — the matrix PHP version must match what your `composer.lock` supports. `composer.json` must also declare the correct minimum (`^8.4`).

**Vite assets not found**
Run `npm install` first, then `npm run dev`.

**Changes not reflected on GitHub Actions**
GitHub runs the committed version of workflow files — local edits have no effect until pushed. Check the file on github.com directly to confirm it matches your local version.

**Larastan type errors**
Run `composer types:check` locally before pushing. All model relationships need explicit return types, e.g.:
```php
public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}
```
