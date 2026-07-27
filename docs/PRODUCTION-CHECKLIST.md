# Production readiness checklist

Work through this before exposing Liberu CMS to the public internet. It closes
the common deployment foot-guns (leaked stack traces, insecure cookies, missing
headers) covered by OWASP A05/A02.

## Application

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` — **never** expose stack traces or config in production.
- [ ] `APP_KEY` is set (`php artisan key:generate`) and kept secret.
- [ ] `APP_URL` is the real HTTPS URL.
- [ ] Config/route/view/event caches built: `php artisan config:cache route:cache view:cache event:cache` (rebuild on every deploy).

## Transport & cookies

- [ ] TLS terminated at the edge; all HTTP redirected to HTTPS.
- [ ] `SESSION_SECURE_COOKIE=true` (cookies only sent over HTTPS).
- [ ] Force HTTPS URL generation behind a proxy — call `URL::forceScheme('https')`
      in a service provider, or set `TrustProxies`/`ASSET_URL` appropriately.
- [ ] HSTS confirmed on responses (`SECURITY_HSTS_ENABLED=true`, sent by the
      `SecurityHeaders` middleware). Consider submitting to the HSTS preload list
      once `SECURITY_HSTS_PRELOAD=true` is safe for the whole domain.

## Security headers (`config/security-headers.php`)

- [ ] `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`,
      `Referrer-Policy` present (default on via the middleware).
- [ ] Content-Security-Policy: ships **report-only** so it cannot break the
      Filament panel. Collect reports, tune `SECURITY_CSP_POLICY`, then set
      `SECURITY_CSP_REPORT_ONLY=false` to enforce.

## Data & workers

- [ ] Database migrated: `php artisan migrate --force`.
- [ ] Baseline roles/permissions seeded (`CmsBaselineRolesSeeder`, run via
      `CoreSetupSeeder`) so the admin can use the panel.
- [ ] Queue worker running (`php artisan queue:work`, or Horizon if enabled) —
      notifications and other queued jobs need it.
- [ ] `php artisan storage:link` created; `storage/` and `bootstrap/cache/`
      writable by the web user only.

## Dependencies

- [ ] `composer install --no-dev --optimize-autoloader`.
- [ ] `composer audit` clean (enforced in CI; `audit.block-insecure` is on).
- [ ] Front-end assets built: `npm ci && npm run build`.

## Verify

- [ ] A public page and the `/app` panel both render over HTTPS.
- [ ] Response headers include the security set above (curl `-I`).
- [ ] Hitting a bad route returns a generic error page, **not** a stack trace.
