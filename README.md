# BRI International School Management System

A PHP school management app (students, teachers, classes, attendance, exams &
marks, fees, user accounts) built with a lightweight custom MVC architecture,
backed by PostgreSQL, and fully dockerized.

> Note: this repo was originally scaffolded with `laravel new`, but the app
> itself does not run through Laravel's `app/`, `routes/`, or `artisan` — the
> real application is a self-contained MVC app in `src/`. The Laravel
> skeleton files remain in the repo but are unused at runtime.

## Architecture

```
src/
  Core/            # Database (PDO/Postgres singleton), Model, Controller, Auth
  Models/          # StudentModel, TeacherModel, SchoolClassModel, AttendanceModel, ...
  Controllers/      # StudentController, TeacherController, DashboardController, ...
  Views/            # One folder per module; layout/header.php + layout/footer.php wrap every page
  bootstrap.php     # autoloader + session bootstrap, loaded by every entry script
db/
  schema.sql         # Postgres DDL
  seed.php            # idempotent seeder with realistic sample data
public/
  index.php              # single front controller — routes every request
  css/, js/, images/, fonts/          # static assets
```

Only `public/` is the web server's document root. `src/` and `db/` sit
outside it, so application code and the seeder are never reachable by a
direct HTTP request no matter how the web server is configured — the only
way in is through `public/index.php`.

`public/index.php` is the single front controller: every request (real or
not) falls through to it via nginx's `try_files ... /index.php` (and the
PHP built-in dev server's equivalent fallback), and a small route table
inside it maps clean paths like `/dashboard/students` to a `Controller`
class and method, which uses a `Model` for data access and renders a
`View`. There are no `.php` extensions in any app URL, and no file-per-page
mapping — the URL space is defined entirely by the route table.

## Running with Docker (recommended)

```bash
docker compose up --build
```

This starts:
- `app` — nginx + PHP-FPM, served at http://localhost:8080
- `postgres` — PostgreSQL 16, exposed on host port 5432 (in case you want to inspect it with a local client)

On boot, the app container waits for Postgres to become healthy, then runs
`db/seed.php`, which creates the schema (if missing) and seeds
sample data. It's safe to restart the stack repeatedly — seeding is
idempotent.

### Demo logins (seeded)

| Username        | Password    | Role    |
|------------------|-------------|---------|
| `admin`          | `admin123`  | admin   |
| `grace.teacher`  | `teacher123`| teacher |
| `peter.teacher`  | `teacher123`| teacher |
| `sarah.bursar`   | `bursar123` | bursar  |

## Deploying to Render

`render.yaml` provisions a Docker web service plus a managed Postgres
database. From the Render dashboard, create a new Blueprint pointing at this
repo — it will pick up `render.yaml` automatically and wire the `DB_*` env
vars from the managed database into the web service.

The container (`Dockerfile` + `.docker/`) runs nginx and PHP-FPM under
supervisord in a single image, listening on port 8080 — the same image is
used for both Render and local `docker compose`. On boot, `.docker/start.sh`
waits for Postgres, runs `db/seed.php` (idempotent), then starts
the server. Render's health check hits `/health.php`, a dependency-free
endpoint that doesn't touch the database.

## Running without Docker

1. Copy `.env.example` to `.env` and point `DB_*` at a Postgres instance you control.
2. `php db/seed.php` to create the schema and seed data.
3. `php -S localhost:8000 -t public` to serve the app, then visit `http://localhost:8000`.

## Notes

- The original codebase's `dashboard/settings.php` was just static template
  demo content with no backend logic. It's been replaced with a real settings
  form (school info + current term/academic year) at `/dashboard/settings`,
  admin-gated, backed by the `settings` key/value table and `SettingModel`.
- Two other original files, `dashboard/dashboard.php` and
  `dashboard/dashboard_2.php`, were unmodified admin-template leftovers with
  no real functionality and weren't linked from the app's navigation. They
  were dropped entirely rather than converted, since the new front
  controller only serves routes it explicitly knows about.
- A printable, per-student report card (subject marks, computed grade/remark)
  is available at `/dashboard/students/report-card?id=<id>` via the "report
  card" icon on the Students page. This started as a Laravel-based feature
  (`app/Http/Controllers/ReportCardController.php` + `barryvdh/laravel-dompdf`,
  merged in from a separate branch of work) but was ported to the same
  browser-print pattern as the fee receipt, since the live app never boots
  Laravel's kernel — `public/index.php` is this app's own front controller,
  not Laravel's. The original Laravel-based files remain in the repo
  alongside the rest of the unused skeleton, unreachable at runtime.
