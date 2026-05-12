# Agent Guide

This file is for future AI agents working on this repository.

## Project Summary

This is a Laravel 11 and Vue 3 website uptime monitoring MVP.

Core assessment requirements:

- Clients are stored in the database with an email address.
- Each client can have multiple websites to monitor.
- The home page is a Vue SPA that shows a client email select input.
- Selecting a client displays that client's active websites as hyperlinks.
- Clicking a website shows a Continue/Cancel confirmation dialog before opening a new tab.
- `monitor:websites` dispatches uptime checks for active websites.
- Checks use Laravel's HTTP client with a 10 second timeout.
- Down/error responses email the client from `do-not-reply@example.com`.
- The scheduler runs monitoring every 15 minutes.

## Important Files

- `routes/web.php`: Web route and `/api/clients` endpoint.
- `routes/console.php`: Laravel scheduler registration.
- `app/Models/Client.php`: Client model and website relationship.
- `app/Models/Website.php`: Website model, casts, URL normalization.
- `app/Http/Controllers/ClientWebsiteController.php`: Client/website JSON API.
- `app/Jobs/CheckWebsiteUptime.php`: Per-website uptime check and email trigger.
- `app/Console/Commands/MonitorWebsitesCommand.php`: Dispatches uptime jobs.
- `app/Mail/WebsiteDownMail.php`: Down-alert email.
- `resources/js/App.vue`: Vue SPA.
- `database/seeders/DatabaseSeeder.php`: Demo clients and websites.
- `tests/Feature/ClientWebsiteTest.php`: Client API and page shell tests.
- `tests/Unit/CheckWebsiteUptimeTest.php`: Uptime job and mail tests.

## Local Commands

Install dependencies:

```bash
composer install
npm install
```

Run migrations and demo seed data:

```bash
php artisan migrate --seed
```

Run the app:

```bash
php artisan serve
```

Run frontend assets:

```bash
npm run dev
```

Build production assets:

```bash
npm run build
```

Run tests:

```bash
php artisan test
```

Format PHP:

```bash
./vendor/bin/pint --dirty
```

Run monitor manually:

```bash
php artisan monitor:websites
```

Run queued jobs locally:

```bash
php artisan queue:work
```

## Environment Notes

- Do not commit `.env`, database files, logs, `vendor`, `node_modules`, or built assets.
- `.env.example` is configured for MySQL/MariaDB.
- Tests use in-memory SQLite via `phpunit.xml`.
- Local mail may use `MAIL_MAILER=log`, which writes generated emails to `storage/logs/laravel.log`.
- Production should use a real Laravel mail driver, such as SES or SMTP.
- Production can use Redis for queues/cache when available.

## Data Rules

- The assessment does not require a public form for adding clients.
- Clients and websites are intended to be manually entered in the database during deployment.
- Demo data can be changed in `DatabaseSeeder`.
- Keep each client to ten or fewer websites to match the requirement.
- Use `firstOrCreate` in seeders to avoid duplicate demo records.

## Implementation Guardrails

- Keep the MVP simple and Laravel-native.
- Prefer Laravel's built-in HTTP client, mail, queues, scheduler, migrations, and tests.
- Preserve the required email format: subject and body must be `{website URL} is down!`.
- Preserve the sender as `do-not-reply@example.com`.
- Preserve the 10 second uptime-check timeout.
- Preserve the 15 minute schedule interval.
- Do not add authentication unless explicitly requested; the assessment says it is not needed.
- Do not add client CRUD UI unless explicitly requested; manual database entry is expected.

## Before Committing

Run:

```bash
./vendor/bin/pint --dirty
php artisan test
npm run build
```

Then check:

```bash
git status -sb
```

Only commit intentional source changes.
