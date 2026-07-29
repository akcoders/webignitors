# WebIgnitors

A ten-page creative agency website built with Laravel 13, Bootstrap 5, and MySQL. It covers web development, iOS and Android applications, digital marketing, and AI integration.

The interface uses a custom art direction system with layered parallax scenes, kinetic typography, interactive 3D cards, magnetic calls to action, scroll reveals, and reduced-motion support. Every page is designed for desktop, tablet, and mobile.

## Laravel Herd setup

```bash
composer install
cp .env.example .env
php artisan key:generate
mysql -u root -e "CREATE DATABASE webignitors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
npm install
npm run build
```

Link the project in Herd, then open `https://webignitors.test`. Use `npm run dev`
while changing frontend files, or `npm run build` for production assets.

The application uses MySQL by default. Set `DB_HOST`, `DB_PORT`, `DB_DATABASE`,
`DB_USERNAME`, and `DB_PASSWORD` in `.env` for your local MySQL installation.

For Laravel's built-in development server instead, run `composer run dev` and
open `http://localhost:8000`.

## Contact form

Project inquiries are validated server-side, protected with a honeypot and rate limit, stored in the `inquiries` table, and emailed to `MAIL_TO_ADDRESS`.

Local development uses Laravel's `log` mailer, so generated emails appear in `storage/logs/laravel.log`. For production, set `MAIL_MAILER` and the related SMTP or transactional provider values in `.env`.

## Quality checks

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```
