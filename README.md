# Workshop Registration Service
A web app and JSON API for a community training centre to run its workshops. Front desk staff register and cancel attendees, managers schedule and edit workshops, and admins manage staff accounts and roles.

- A workshop can never hold more active registrations than its capacity, even when several staff book the last seat at the same moment.
- Cancelled registrations are never deleted, and every change on the platform is recorded in an audit trail: who did what, and when.

## Versions
- Laravel: 13
- PHP: 8.3 or higher

## Requirements
- PHP 8.3+ with the `pdo_mysql` extension
- Composer 2
- Node.js 20+ and npm
- MySQL 8 or MariaDB 10.4+ (XAMPP works)

## Running the project
1. Create the database:

    ```sql
    CREATE DATABASE workshop_registration CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ```

    If your MySQL user isn't `root` with no password, update `DB_USERNAME` and `DB_PASSWORD` in `.env` after the next step.

2. Install, set up and start the app:

    composer install
    npm install

    cp .env.example .env
    php artisan key:generate

    php artisan migrate --seed

    npm run build
    php artisan serve

3. Open http://localhost:8000.

To run the tests, first create a `workshop_registration_testing` database, then run `php artisan test`.

## Credentials
All accounts use the password `password`.

- Admin | `admin@example.com` | `password`
- Manager | `manager@example.com` | `password`
- Staff | `staff@example.com` | `password`