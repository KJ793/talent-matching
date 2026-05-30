# Intelligent Talent Matching Platform

A web application for matching job seekers with employers, built for CSIT314.

## Tech stack

- PHP 8.x (no framework, light OOP)
- MySQL 8
- Vanilla HTML/CSS/JavaScript (no build step)
- PHPUnit for testing
- GitHub Actions for CI

## Project structure

```
.                  Web-accessible PHP entry points (index.php, login.php, jobs.php, ...)
candidate/         Pages for candidate users
employer/          Pages for employer users
assets/            CSS, JS, images
src/               Application classes (repositories, helpers) — denied direct web access
config/            Configuration files — denied direct web access
database/          SQL schema, seed data, migrations — denied direct web access
tests/             PHPUnit test cases — denied direct web access
docs/              Project documentation
.github/           CI workflow definitions
bootstrap.php      Class autoloader (included by every entry point)
```

## Setup

### 1. Database

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

If you already have a Phase-1 database, apply the migration instead of recreating:

```bash
mysql -u root -p talent_matching < database/migrations/001_add_membership_and_profile_fields.sql
```

### 2. Configuration

```bash
cp config/config.example.php config/config.php
# edit config/config.php to set your DB credentials
```

### 3. Run the app

You have two options. Either works.

**Option A — XAMPP (easiest on Windows/Mac):**
1. Copy this whole folder into `xampp/htdocs/` (e.g. `xampp/htdocs/final_project/`).
2. Start MySQL from the XAMPP control panel.
3. Start Apache from the XAMPP control panel.
4. Open `http://localhost/final_project/` in your browser.

**Option B — PHP's built-in server:**
```bash
php -S localhost:8000
```
Then open `http://localhost:8000/` in your browser.

### 4. Test users (from seed data)

All seeded users have password `password123`.

| Email                | Role      | Membership |
|----------------------|-----------|------------|
| alice@example.com    | candidate | free       |
| bob@example.com      | candidate | premium    |
| acme@example.com     | employer  | free       |
| globex@example.com   | employer  | premium    |

## Testing

The web application itself does **not** require Composer — it runs out of
the box thanks to `bootstrap.php`. Composer is only needed if you want to
run the test suite locally:

```bash
composer install
composer test
# or directly:
vendor/bin/phpunit --testdox
```

The CI pipeline (GitHub Actions) automatically runs the tests on every
push and pull request, so installing PHPUnit locally is optional.

## CI

GitHub Actions runs PHP linting and the full PHPUnit suite on every push
and pull request to `main`. See `.github/workflows/ci.yml`.

## Authors

- Kyle Watchers
- Noah King
- Jacob Morris
- Seif Ali
- Scott Van Hoven
