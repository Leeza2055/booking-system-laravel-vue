# LATER.md — Deferred Decisions

Things intentionally left out of current scope. Revisit after core is complete.

---

## Schema
- [ ] Extract `providers.department` to a `departments` table if department-browse or metadata is needed
- [ ] Add soft deletes to providers and services (currently only bookings are soft-deleted)
- [ ] Consider `restrictOnDelete` → soft delete approach for providers with booking history

## Architecture
- [ ] Extract REST API layer with Sanctum token auth if a mobile app is added
- [ ] Migrate roles to Spatie Laravel Permission if role granularity increases (provider sub-roles, admin tiers)
- [ ] Switch test database from in-memory SQLite to MySQL if MySQL-specific queries are needed

## CI
- [ ] Re-add PHP 8.3 to CI matrix when Symfony dependencies support it

## Features
- [ ] Multi-tenant / multi-clinic support
- [ ] Video consultation link on bookings
- [ ] Prescription / session notes system
- [ ] Customer booking history UI
- [ ] Provider earnings dashboard
- [ ] SMS notifications (alongside email)
- [ ] Recurring / repeat appointments
- [ ] Waitlist when all slots are full
- [ ] Cancellation and refund workflow

## Product (if taken to market)
- [ ] Public shareable provider profile page
- [ ] Provider self-onboarding flow
- [ ] Freemium tier (limited bookings/month free)
- [ ] Transaction fee model (small % cut on Khalti payments)
- [ ] Analytics dashboard for admins
