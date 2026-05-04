# Travel Booking System

Basic academic-ready booking platform built with PHP, MySQL, JavaScript and Bootstrap.

## Main modules

- Authentication with `user` and `admin` roles
- Profile management with soft delete and reactivation
- Trip CRUD with search, filters and pagination
- Reservation CRUD with overbooking and conflict validations
- User dashboard and admin dashboard
- CSV and PDF export examples
- Basic REST API with bearer token authentication
- Activity logs and basic request throttling for sensitive actions

## Local run with XAMPP

1. Copy [.env.example](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/.env.example:1) to `.env`.
2. Create the database `travel_booking_system` in MySQL.
3. Update `.env` if your MySQL credentials or base URL differ.
4. Run `/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/migrate.php`
5. Run `/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/seed.php`
6. Point Apache to `/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/public` or visit `/Travel_Booking_System/public`.
7. Default accounts:
   - `admin@travelbooking.local` / `Admin123!`
   - `user@travelbooking.local` / `User123!`

## Web setup

- You can also open `/setup` in the browser to configure `.env`, test the database connection, run migrations and seed demo data.

## Database versioning

- Versioned migrations live in [database/migrations](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/migrations).
- Use [database/migrate.php](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/migrate.php:1) to apply pending schema changes.
- Use [database/seed.php](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/seed.php:1) to load demo data.
- [database/schema.sql](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/schema.sql:1) remains as a full snapshot reference.

## Tests

- Run `/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/tests/run.php`
- Current tests cover the lightweight framework core: env loading, validation and request handling.

## Deployment

- Hosting notes are documented in [docs/DEPLOYMENT.md](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/docs/DEPLOYMENT.md:1).

## API token

You can generate a token from the profile page or by calling `POST /api/auth/login` with JSON credentials.

## Important note

This project intentionally uses a lightweight custom MVC base because the workspace started empty and Composer/Laravel scaffolding was not available in-path during generation. The structure is ready to be migrated to Laravel later if required by your academic delivery.
