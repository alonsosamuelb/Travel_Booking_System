# Deployment notes

## Local XAMPP

1. Copy [.env.example](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/.env.example:1) to `.env`.
2. Update database credentials and `APP_BASE_URL`.
3. Create the MySQL database.
4. Run:

```bash
/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/migrate.php
/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/database/seed.php
```

5. Make sure Apache serves [public/index.php](/Applications/XAMPP/xamppfiles/htdocs/Travel_Booking_System/public/index.php:1).

## Shared hosting checklist

- Upload the project files.
- Point the domain or subfolder to the `public/` directory.
- Create `.env` with production database credentials.
- Set `APP_ENV=production`.
- Set `APP_DEBUG=false`.
- Ensure `public/uploads/trips` is writable.
- Run migrations and seed manually through PHP CLI if available.

## Recommended production values

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Europe/Madrid
```
