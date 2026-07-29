# WebIgnitors server deployment

This package contains production Composer dependencies and compiled frontend
assets. Composer, Node.js, and npm are not required on the destination server.

## Requirements

- PHP 8.3 or newer
- MySQL 8+ or a compatible MariaDB server
- PHP extensions required by Laravel, including PDO MySQL
- Apache with `mod_rewrite` or an equivalent Nginx configuration

## Install

1. Upload and extract the package outside the public web directory when
   possible.
2. Set the domain document root to the package's `public/` directory. Never
   expose the Laravel project root.
3. Copy `.env.production.example` to `.env`.
4. Add the production URL, MySQL credentials, and SMTP credentials to `.env`.
5. Make `storage/` and `bootstrap/cache/` writable by the web-server user.
6. Run:

   ```bash
   php artisan key:generate --force
   php artisan migrate --force
   php artisan optimize
   ```

7. Open the website and submit one test inquiry. Contact submissions are saved
   in the MySQL `inquiries` table and sent to `MAIL_TO_ADDRESS`.

## Updating an existing installation

Preserve the server's `.env` file and `storage/` directory when replacing
application files. Then run:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

## Shared hosting

In cPanel or a similar panel, create the MySQL database and user first, grant
that user all privileges on the database, and copy the generated values into
`.env`. Configure the domain's document root to `webignitors/public`.
