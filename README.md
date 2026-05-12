# Website Uptime Monitoring Software

Laravel 11 and Vue 3 MVP for monitoring client website homepages.

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- MySQL or MariaDB
- Redis for production queue/cache use

## Features

- Client emails and website lists stored in the database.
- Vue single page home screen with a client email select input.
- Active client websites shown as hyperlinks with a confirmation dialog before opening a new tab.
- `monitor:websites` command dispatches queued checks for active websites.
- Each website check uses Laravel's HTTP client with a 10 second timeout.
- Failed checks or HTTP error responses mark the website down and email the client from `do-not-reply@example.com`.
- Scheduler runs the monitor command every 15 minutes.
- Feature and unit tests cover the client API, shell route, uptime checks, and down emails.

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Open the app at the URL printed by `php artisan serve`, usually `http://127.0.0.1:8000`.

For MAMP/MySQL, create a `website_uptime_monitoring` database and update `.env` with your local credentials. Common MAMP ports are `3306` or `8889`. The example environment is configured for MySQL/MariaDB; tests use in-memory SQLite.

## Environment

Important `.env` values:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=website_uptime_monitoring
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
MAIL_FROM_ADDRESS=do-not-reply@example.com
```

For production, configure Laravel's built-in mail driver for your provider, such as SES, and set `QUEUE_CONNECTION=redis` when Redis is available.

## Managing Clients

The assessment requires client records to be manually entered into the database during deployment. There is no public form for adding clients.

You can add data with SQL, a seeder, or Tinker:

```bash
php artisan tinker
```

```php
$client = App\Models\Client::create(['email' => 'client@example.com']);
$client->websites()->create(['url' => 'https://example.com']);
```

## Monitoring

Run checks manually:

```bash
php artisan monitor:websites
```

In local development, use the database queue worker:

```bash
php artisan queue:work
```

Production scheduling needs the standard Laravel scheduler cron entry:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

In production with Redis:

```bash
php artisan queue:work redis
```

## Tests

```bash
php artisan test
```

## Build Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```
