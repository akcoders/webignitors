# WebIgnitors server deployment

The production package may include Composer dependencies and compiled frontend
assets, so Composer, Node.js and npm are not required on the destination server.

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
4. Add the production URL, MySQL credentials, SMTP credentials and audit API
   values to `.env`. `APP_URL` should be `https://webignitors.in`, without
   `/public`.
5. Make `storage/` and `bootstrap/cache/` writable by the web-server user.
6. Run:

   ```bash
   php artisan key:generate --force
   php artisan migrate --force
   php artisan optimize
   ```

7. Configure the queue cron job described below.
8. Open the website and submit one test inquiry. Contact submissions are saved
   in the MySQL `inquiries` table and sent to `MAIL_TO_ADDRESS`.
9. Register a test account, verify its email, run one website report and confirm
   that the private PDF downloads.

## Website audit APIs

Recommended production values:

```dotenv
PAGESPEED_ENABLED=true
GOOGLE_PAGESPEED_API_KEY=YOUR_GOOGLE_API_KEY
PAGESPEED_TIMEOUT=90
CRUX_ENABLED=true
GOOGLE_CRUX_API_KEY=
W3C_VALIDATOR_ENABLED=true
MDN_OBSERVATORY_ENABLED=true
BROWSERLESS_ENABLED=false
BROWSERLESS_API_TOKEN=
DB_QUEUE_RETRY_AFTER=390
```

PageSpeed can answer without a key at a limited shared quota, but a Google API
key is strongly recommended. CrUX uses `GOOGLE_PAGESPEED_API_KEY` when its own key
is empty. Browserless is optional; Lighthouse provides a final-render screenshot
fallback. Do not disable `AUDIT_RESOLVE_DNS` in production because it protects the
server from private-network and loopback URL requests.

## Queue worker on shared hosting

Website reports run in the database queue so that PageSpeed and validation APIs
do not hold open the visitor's browser request. Add a cron job that runs every
minute:

```bash
cd /home/u897223014/domains/webignitors.in/public_html && php artisan queue:work --stop-when-empty --tries=2 --timeout=330 --max-time=350
```

If the hosting panel needs an absolute PHP binary, use the PHP 8.3 CLI path shown
by the provider. Never run two persistent workers on a shared-hosting plan; the
short `--stop-when-empty` command is designed for cron.

After deploying, these MySQL tables should exist:

```text
users
password_reset_tokens
sessions
jobs
job_batches
failed_jobs
website_reports
website_report_pages
website_report_findings
website_audit_api_runs
inquiries
cache
cache_locks
```

Report JSON, screenshots and PDF files are private in
`storage/app/private/reports`. Keep the entire `storage` directory writable and
do not expose it as a public document root.

## Updating an existing installation

Preserve the server's `.env` file and `storage/` directory when replacing
application files. Then run:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

Restarting a persistent queue worker is only needed when one is configured:

```bash
php artisan queue:restart
```

## Shared hosting

In cPanel or a similar panel, create the MySQL database and user first, grant
that user all privileges on the database, and copy the generated values into
`.env`. Test the exact credentials before migrating:

```bash
php artisan config:clear
php artisan migrate:status
```

Configure the domain document root to the Laravel `public/` directory. If the
project itself is uploaded to `public_html`, the preferred document root is
`public_html/public`; the site URL should not contain `/public`.
