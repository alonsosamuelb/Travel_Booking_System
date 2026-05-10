# Travel Booking System

Final project for 2º DAW based on a web application for managing travel bookings between users.

This project has been developed as part of the final academic delivery of the Higher Technician in Web Application Development. Its purpose is to provide a functional booking management system that applies the main concepts studied during the course, including backend development, frontend validation, database design, role-based access and responsive interface design.

## Project objectives

- Develop a complete web application with authentication and user roles
- Manage trips and reservations through a structured CRUD system
- Apply frontend and backend validation
- Work with a relational database model
- Provide separate user and admin areas
- Deliver a project ready to run locally and suitable for academic presentation

## Main features

- User registration and login
- Password recovery and account reactivation
- Roles: `user` and `admin`
- Profile management
- Soft delete for accounts
- Trip CRUD
- Reservation CRUD
- Reservation history
- User dashboard
- Admin dashboard
- CSV and PDF export
- Basic REST API
- Users can publish trips as drivers

## Technologies used

- PHP
- MySQL / MariaDB
- JavaScript
- Bootstrap 5
- HTML5
- CSS3

## Project structure

```text
app/
config/
database/
docs/
public/
resources/views/
routes/
tests/
```

## Installation

1. Copy `.env.example` to `.env`
2. Review the database settings in `.env`
3. Run the installation script
4. Open the project in the browser

### Commands

```bash
cp .env.example .env
php database/install.php
```

If you are using XAMPP on macOS:

```bash
cp .env.example .env
/Applications/XAMPP/xamppfiles/bin/php database/install.php
```

## Local URL

```text
http://localhost/Travel_Booking_System/public
```

## Demo accounts

### Admin
- Email: `admin@travelbooking.local`
- Password: `Admin123!`

### User
- Email: `user@travelbooking.local`
- Password: `User123!`

## Additional notes

- The project includes a full database snapshot in `database/schema.sql`
- Trip images are stored in `public/uploads/trips`
- If image uploads fail in local XAMPP, check write permissions for that folder

## Documentation

- `docs/API.md`
- `docs/ERD.md`
- `docs/DEPLOYMENT.md`

## Author

Samuel Buitrago Alonso
