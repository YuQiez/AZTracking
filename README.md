# AZTrack

AZTrack is a small Laravel-based backend for tracking orders, customers, statuses and feedback.

This README documents how to set up, run, and test the project locally and shows quick API examples used during development.

## Requirements

- PHP >= 8.1 with common extensions (PDO, mbstring, OpenSSL, tokenizer, xml)
- Composer
- Node.js + npm (for frontend assets if required)
- A database (sqlite, MySQL, or Postgres). The project uses an in-memory sqlite database for automated tests by default.

## Quick setup (development)

1. Install dependencies

```bash
composer install
npm install    # optional, only if working on frontend assets
```

2. Environment

```bash
cp .env.example .env
php artisan key:generate
# adjust DB_ settings in .env if you want to use MySQL/Postgres
```

3. Run migrations and seed initial data

```bash
php artisan migrate
php artisan db:seed
```

The repository contains a `UserSeeder` that creates an `admin@example.com` user (password `admin1234`) and assigns the `admin` role.

4. Run the app

```bash
php artisan serve --port=8000
```

## API overview

All API routes are defined in `routes/api.php` and protected by Sanctum authentication and permission middleware.

Common endpoints (authenticated):

- `POST /api/login` — accepts `{ name, password }` where `name` can be email or username. Returns an access token.
- `POST /api/logout` — revokes current token.

Resource routes (examples):

- Users: `GET /api/users`, `POST /api/users/store`, `GET /api/users/{id}`, `POST /api/users/{id}/update`, `DELETE /api/users/{id}/delete`
- Customers: `GET /api/customers`, `POST /api/customers/store`, `GET /api/customers/{id}`, ...
- Orders: `GET /api/orders`, `POST /api/orders/store`, `GET /api/orders/{id}`, ...
- Statuses, Feedbacks, Roles, Permissions follow the same pattern.

Example: login and create an order (using curl)

```bash
# login
curl -X POST http://127.0.0.1:8000/api/login \
	-H 'Content-Type: application/json' \
	-d '{"name":"admin@example.com","password":"admin1234"}'

# take the returned access_token and use it as Bearer token
TOKEN="<access_token>"

# create an order
curl -X POST http://127.0.0.1:8000/api/orders/store \
	-H "Authorization: Bearer $TOKEN" \
	-H 'Content-Type: application/json' \
	-d '{"name":"Test Order","address":"123 Main St","customer_id":1,"status_id":1}'
```

## Testing

- Unit tests: `./vendor/bin/phpunit tests/Unit`
- Feature tests (use sqlite or configure DB in `phpunit.xml`): `./vendor/bin/phpunit tests/Feature`

Note: If your environment lacks the sqlite PDO driver tests may fail — install `php-sqlite3` for your PHP version or configure `phpunit.xml` to use a different test database.

## Development notes

- Controllers use a consistent pattern: accept `id` either from route param or `?id=` query, return 400 when `id` missing, use `findOrFail` and map `ModelNotFoundException` to 404.
- Permissions are enforced via Spatie permission middleware (guard `sanctum`). Seeds create an `admin` role; use tinker to add or adjust permissions.

## Troubleshooting

- 403 Forbidden on API requests: ensure the authenticated user has the required permission, e.g. `view orders`, `create orders`.
- `could not find driver` when running tests: install SQLite PDO extension (`php-sqlite3`) or update `phpunit.xml` to a supported DB.


