# Task Manager Specification

## Vision
A portfolio-grade Task Manager that highlights API-driven architecture and modern SPA ergonomics. The solution emphasises strong authentication, clean project/task management, and readiness for future collaboration features.

## Core Domains
- **Project**: title, description, due date, status (`draft`, `active`, `completed`, `archived`).
- **Task**: content, assignee, deadline, priority, status (`todo`, `in_progress`, `blocked`, `done`).
- **Relationships**: projects own many tasks; tasks belong to a single project and reference a user as assignee.

## Technology Stack
- **Backend**: Laravel 11 API with Sanctum for SPA token auth.
- **Frontend**: Nuxt 3 + TypeScript + Pinia SPA, VeeValidate for form handling, dark-mode native styling.
- **Infrastructure**: Docker Compose orchestrating php-fpm, nginx, MySQL 8, and the Nuxt dev server.
- **Testing**: PHPUnit or Pest for APIs, Vitest for unit/component tests, Cypress for end-to-end validation.
- **CI/CD**: GitHub Actions pipelines targeting Render (or similar) for automated deployment.

## Non-functional Targets
- Lighthouse score ≥ 90 across Performance, Accessibility, Best Practices, SEO.
- OWASP Top 10 considerations: input sanitisation, authentication hardening, rate limiting, secure headers.
- Developer experience: hot reload, containerised tooling, typed contracts, observable test coverage.

## Future Enhancements
- Interactive Gantt charts for project planning.
- Comment threads with mentions and rich text.
- Project sharing and RBAC-driven access control.
- Real-time notifications and activity feeds.

## Implementation Milestones
1. Stand up migrations, seed data, and RESTful routes for Projects/Tasks.
2. Implement Sanctum-authenticated session flow between Nuxt and Laravel.
3. Build Pinia stores and UI flows for CRUD, filtering, and prioritisation.
4. Layer validation (VeeValidate hooks), telemetry, and automated test suites.
5. Optimise and harden for deployment (GitHub Actions, Render infrastructure, security review).
