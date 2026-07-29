# WebIgnitors

A creative agency website and private website-intelligence platform built with Laravel 13, Bootstrap 5, and MySQL. It covers specialist eCommerce, ERP, CRM, HRM, complex web applications, iOS and Android products, digital marketing, and AI-powered business automation.

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

## Website intelligence reports

`/website-audit` accepts a public website URL and creates a private queued report
after signup or sign-in. Reports combine:

- Google PageSpeed Insights/Lighthouse mobile and desktop audits
- Chrome UX Report real-user data when a Google API key and sufficient samples
  are available
- W3C HTML validation
- MDN HTTP Observatory security-header checks
- WebIgnitors content, design, code, accessibility, SEO, marketing and automation
  heuristics
- Optional Browserless API screenshots, with Lighthouse screenshots as fallback

The application stores report history, page evidence, detailed findings, API-run
history and status in MySQL. Raw report JSON and branded PDFs are stored privately
under `storage/app/private/reports` and are only served through authenticated,
owner-checked routes. Email verification is required to download a PDF.

Add `GOOGLE_PAGESPEED_API_KEY` to `.env` for dependable PageSpeed quota. The same
key is used for CrUX unless `GOOGLE_CRUX_API_KEY` is set. Browserless is optional:
set `BROWSERLESS_ENABLED=true` and `BROWSERLESS_API_TOKEN` only when full-page API
screenshots are required.

Start a local worker while testing reports:

```bash
php artisan queue:work --tries=2 --timeout=330
```

No VPS is required. On shared hosting, run the worker as a once-per-minute cron
job with `--stop-when-empty`; see `DEPLOYMENT.md`.

## Quality checks

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```
