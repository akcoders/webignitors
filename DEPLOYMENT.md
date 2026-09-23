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
8. Create the first administrator as described below.
9. Open the website and submit one test inquiry. Contact submissions are saved
   in the MySQL `inquiries` table and sent to `MAIL_TO_ADDRESS`.
10. Register a test account, verify its email, run one website report and confirm
   that the private PDF downloads.

## Administrator access

The administrator console is available at `/admin/login`. No default password is
included. After running the migrations, create the first administrator over SSH:

```bash
php artisan admin:create info@webignitors.in "Anuj Shukla"
```

Enter and confirm a password of at least 12 characters containing uppercase and
lowercase letters and numbers. If the email already belongs to a customer, this
command safely promotes that account and resets its password. Never place an
administrator password in `.env`, source control or a shell command.

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

In Google Cloud, enable both **PageSpeed Insights API** and
**Chrome UX Report API** on the same project, then create one API key restricted
to those two APIs. The same key is sufficient for both application settings.

## Bot protection and report allowance

Public contact, audit, registration, login, password-recovery, email-verification
and administrator-login forms support Cloudflare Turnstile. In Cloudflare, open
**Turnstile**, create a Managed widget, and allow `webignitors.in` (plus
`www.webignitors.in` if that hostname is used). Add the generated site key and
secret key to `.env`, then enable it:

```dotenv
TURNSTILE_ENABLED=true
TURNSTILE_SITE_KEY=YOUR_PUBLIC_SITE_KEY
TURNSTILE_SECRET_KEY=YOUR_PRIVATE_SECRET_KEY
TURNSTILE_ALLOWED_HOSTNAME=webignitors.in,www.webignitors.in
TURNSTILE_TIMEOUT=10
```

Run `php artisan optimize:clear && php artisan optimize` after changing these
values. Keep the secret key private. The browser widget alone is not trusted;
every token is verified server-side with Cloudflare and checked against its form
action and production hostname.

Each user can create one website report in a rolling 24-hour period. This limit
is enforced inside the report service with a database lock, including audits
continued through registration or login.

## Production email

Registration sends an email-verification link. For Hostinger Email, use the full
mailbox address as the username and the mailbox password—not the hPanel account
password:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_TIMEOUT=20
MAIL_USERNAME=info@webignitors.in
MAIL_PASSWORD="YOUR_MAILBOX_PASSWORD"
MAIL_FROM_ADDRESS=info@webignitors.in
MAIL_FROM_NAME="WebIgnitors"
```

`MAIL_FROM_ADDRESS` is the sender shown on outgoing messages. It does not set
the customer recipient. `MAIL_TO_ADDRESS` is used only for the internal copy of
a contact inquiry. Contact confirmations go to the email entered in the form,
while verification, password-reset and report-ready emails go to the address in
the customer's `users.email` record.

Hostinger also supports implicit SSL on port 465; use `MAIL_SCHEME=smtps` for
that combination. Port 587 uses `MAIL_SCHEME=smtp`, and the mailer negotiates
STARTTLS automatically. Do not use `MAIL_SCHEME=tls`: `tls` is not a supported
Symfony Mailer transport scheme. Quote the password when it contains spaces,
`#`, `$` or other characters that `.env` may interpret.

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

Before creating the cron entry, run its worker command once over SSH. A queued
report should move from `queued` to `processing` and finally `completed`:

```bash
php artisan queue:work --stop-when-empty --tries=2 --timeout=330 -vvv
```

The API key does not execute queued reports. If reports remain at “Waiting for
the audit worker”, the cron/worker is not running.

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

The `users.is_admin` column is added by migration and defaults to `false`, so
normal registration can never grant administrator access.

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

## Registration or report troubleshooting

Never paste secret values into support messages. These commands show whether
the required settings are loaded without printing their contents:

```bash
php artisan optimize:clear
php artisan tinker --execute="dump([
    'queue' => config('queue.default'),
    'pagespeed_key' => filled(config('audit.pagespeed.api_key')) ? 'set' : 'missing',
    'crux_key' => filled(config('audit.crux.api_key')) ? 'set' : 'missing',
    'mail_driver' => config('mail.default'),
    'mail_host' => config('mail.mailers.smtp.host'),
]);"
php artisan tinker --execute="dump([
    'queued_reports' => App\\Models\\WebsiteReport::where('status', 'queued')->count(),
    'processing_reports' => App\\Models\\WebsiteReport::where('status', 'processing')->count(),
    'jobs' => Illuminate\\Support\\Facades\\DB::table('jobs')->count(),
    'failed_jobs' => Illuminate\\Support\\Facades\\DB::table('failed_jobs')->count(),
]);"
php artisan queue:failed
tail -n 100 storage/logs/laravel.log
```

Interpretation:

- A queued report plus a row in `jobs` means the queue worker/cron has not run.
- A row in `failed_jobs` means the worker ran but the exception must be fixed;
  use `php artisan queue:retry all` after correcting it.
- A user with no report often means registration stopped at email delivery on an
  older build. Sign in with the registered credentials and submit the URL again.
- `Connection could not be established with host smtp...` is an email setting
  issue, not an audit API issue.
