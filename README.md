# Task Manager Monorepo

Task Manager App is a portfolio-friendly project that pairs a Laravel 11 API with a Nuxt 3 SPA. The repository ships as a Dockerised monorepo so you can spin up web, API, and database tiers consistently across machines.

## Repository Layout

- `backend/` — Laravel API source (created via `composer create-project` inside the container)
- `frontend/` — Nuxt 3 SPA with TypeScript and Pinia scaffolding under `src/`
- `docker/` — Service definitions for php-fpm and nginx
- `docs/` — Project documentation and specifications

## Prerequisites

- Docker Desktop 4.20+ (or Docker Engine + Docker Compose v2)
- Make (GNU Make 3.81+)
- Node.js 20+ (optional for local tooling outside Docker)

## First-time Setup

1. Copy the environment template and build images:
   ```bash
   make setup
   ```
2. Create the Laravel skeleton inside `backend/` if it is missing:
   ```bash
   docker compose run --rm backend composer create-project laravel/laravel .
   ```
3. Generate the application key and link storage:
   ```bash
   docker compose run --rm backend php artisan key:generate
   docker compose run --rm backend php artisan storage:link
   ```
4. Install frontend dependencies (run once whenever `package.json` changes):
   ```bash
   docker compose run --rm frontend npm install
   ```

## Daily Development Flow

- Bring everything online (nginx, php-fpm, mysql, nuxt):
  ```bash
  make up
  ```
  Laravel API → http://localhost:8080/api, Nuxt SPA → http://localhost:3000
- Stop containers:
  ```bash
  make down
  ```
- Tail logs:
  ```bash
  make logs
  ```

## Testing & Quality Gates

- Backend (PHPUnit & Pest): `docker compose run --rm backend php artisan test`
- Frontend unit tests (Vitest): `docker compose run --rm frontend npm run test:unit`
- Cypress e2e: `docker compose run --rm frontend npm run test:e2e`
- Static analysis: `docker compose run --rm backend ./vendor/bin/pint` and `docker compose run --rm frontend npm run lint`

## Database & Tooling

- Database shell: `make mysql-shell`
- Backend Tinker shell: `docker compose run --rm backend php artisan tinker`
- Frontend interactive shell: `make frontend-shell`

## Next Steps

With the infrastructure in place, move on to defining database migrations, seeders, Sanctum auth flow, and REST API routes. Mirror API contracts on the Nuxt side via typed stores and composables, then layer in testing (Vitest components, Cypress journeys) to satisfy the specification and Lighthouse/OWASP requirements.
