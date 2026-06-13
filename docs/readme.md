# Booking System — Laravel + Vue

A generic professional appointment booking platform, demoed as a clinic booking system. Built so the core engine can be reskinned for any person-based booking domain — tutors, lawyers, fitness coaches, consultants — without touching the database.

## Live Demo
> _Add link after deployment (Week 8)_

## Tech Stack
| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13.x, PHP 8.4+ |
| Frontend | Vue 3 + Inertia.js |
| Database | MySQL (SQLite for tests) |
| Payments | Khalti sandbox |
| Queue | Laravel queues (email notifications) |
| CI | GitHub Actions — PHP 8.4 and 8.5 |

## Roles
| Role | Description |
|------|-------------|
| `admin` | Manages providers, users, platform settings |
| `provider` | Sets availability, manages services, views bookings |
| `customer` | Browses providers, books appointments, makes payments |

## Quick Start
```bash
git clone https://github.com/Leeza2055/booking-system-laravel-vue
cd booking-system-laravel-vue
composer install
cp .env.example .env
php artisan key:generate
# Set DB credentials in .env, then:
php artisan migrate --seed
npm install && npm run dev
php artisan serve
```
Visit http://localhost:8000

## Features
- [ ] Role-based auth (admin / provider / customer)
- [ ] Provider profiles with services and weekly availability
- [ ] Slot generation with double-booking prevention
- [ ] Appointment booking with status workflow
- [ ] Khalti payment integration (sandbox)
- [ ] Email confirmations via background jobs
- [ ] Admin dashboard
- [ ] Feature tests (Pest)

## Project Docs
| File | Contents |
|------|----------|
| `docs/architecture.md` | Stack decisions, folder structure, design rationale |
| `docs/database.md` | Schema, field decisions, relationships |
| `docs/setup.md` | Local environment setup steps |
| `docs/api.md` | API endpoints _(fill as routes are built)_ |
| `docs/khalti.md` | Payment integration notes _(Week 5)_ |
| `LATER.md` | Deferred decisions and future features |
