# 06 — Security: production config + HTTP security headers

**What to build:** Ship safe-by-default production configuration and HTTP security headers, closing OWASP A05 (and A02 transport hardening). Removes the "deployment copied from `.env.example` leaks stack traces" foot-gun.

**Blocked by:** 04 — bundled with the security workstream; independent of 05.

**Status:** DONE (branch `feature/cms-security-headers`, not pushed)

- [x] Production guidance in `.env.example`: comment on `APP_ENV`/`APP_DEBUG` (prod = production + false, links the checklist); added `SESSION_SECURE_COOKIE=false` (comment: true in prod) + `SECURITY_HSTS_ENABLED`/`SECURITY_CSP_ENABLED`/`SECURITY_CSP_REPORT_ONLY`. Kept local defaults working (local/true) — the prod flip lives in the checklist. `URL::forceScheme('https')` / edge HSTS documented there.
- [x] `App\Http\Middleware\SecurityHeaders` (config-driven, new `config/security-headers.php`) appended **globally** in `bootstrap/app.php` (web + API): HSTS (gated), `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, and **CSP report-only by default** (flips to enforcing when `SECURITY_CSP_REPORT_ONLY=false`); policy allows `'unsafe-inline'`/`'unsafe-eval'` + `frame-ancestors 'none'`.
- [x] Headers don't break panel/public — tests assert `/` and `/app/login` render 200 with headers.
- [x] `docs/PRODUCTION-CHECKLIST.md` written (debug off, HTTPS/HSTS/forceScheme, secure cookies, caches, migrate + baseline seed, queue worker, storage perms, `--no-dev`, `composer audit`, APP_KEY, npm build, no-stack-trace verify).
- [x] `tests/Feature/SecurityHeadersTest.php` (5): headers on public + panel; CSP report-only (enforcing header absent); enforcing variant when report-only off; HSTS omitted when disabled.
- [x] DoD: headers present + tested; checklist written; Pint + PHPStan max clean; full suite **548 green on SQLite** (MySQL + Postgres via ticket-03 CI matrix).
