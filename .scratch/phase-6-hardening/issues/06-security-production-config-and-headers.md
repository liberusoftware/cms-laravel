# 06 — Security: production config + HTTP security headers

**What to build:** Ship safe-by-default production configuration and HTTP security headers, closing OWASP A05 (and A02 transport hardening). Removes the "deployment copied from `.env.example` leaks stack traces" foot-gun.

**Blocked by:** 04 — bundled with the security workstream; independent of 05.

**Status:** ready-for-agent

- [ ] Production defaults: `.env.example` → `APP_DEBUG=false`, `APP_ENV=production` guidance; `SESSION_SECURE_COOKIE=true` for non-local; document `URL::forceScheme('https')` / edge HSTS.
- [ ] A `SecurityHeaders` middleware applied to web (and API) responses: `Strict-Transport-Security`, `X-Frame-Options: DENY` (or frame-ancestors), `X-Content-Type-Options: nosniff`, `Referrer-Policy`, and a **Content-Security-Policy in report-only mode initially** (Filament/Livewire rely on inline scripts — enforcing CSP outright breaks the panel; ship report-only, tune, then flip to enforcing in a later pass).
- [ ] Headers must not break the Filament panel or the public site; test both still render.
- [ ] A **production-readiness checklist** doc (debug off, HTTPS/HSTS, secure cookies, queue worker, storage perms, `composer audit` clean, APP_KEY set). *(Docs file — explicitly in scope for this ticket.)*
- [ ] Behavior guarantees: responses carry the expected security headers (feature test asserting header presence on a public + a panel route); CSP is report-only and does not block panel assets.
- [ ] DoD: headers present + tested; prod checklist written; Pint + PHPStan max + full suite green.
