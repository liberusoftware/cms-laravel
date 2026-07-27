# 06 — Delivery API README

**What to build:** A `cms-api` README that lets a consumer or operator use the Delivery API without a formal spec — documenting every route, authentication, token lifecycle, response shapes, pagination, and rate limits across the whole stable surface.

**Blocked by:** 02 (Posts), 03 (Menus), 04 (Content-Entries), 05 (rate limiting) — the surface must be complete before it's documented.

**Status:** ready-for-agent

- [ ] README in `cms-api` documenting all `/api/v1` routes: Pages, Posts (+taxonomy filters), Menus (by location), Content-Entries (by type / slug).
- [ ] Auth section: how to issue a Delivery token (`cms-api:issue-token`), how to present it (bearer), and how to revoke it.
- [ ] Response shapes: sanitized `content`, embedded featured media, embedded taxonomy, pagination metadata + `per_page` cap.
- [ ] Rate-limit behavior: default budget, `429` + `Retry-After`, per-token keying.
- [ ] Notes what is deliberately out of scope for v1 (write/CRUD, search, generic query grammar, standalone media, OpenAPI) so consumers don't expect it.
- [ ] Formal OpenAPI generation stays deferred (explicitly noted as a future follow-up).
