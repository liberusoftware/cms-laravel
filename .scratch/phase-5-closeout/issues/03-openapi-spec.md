# 03 — OpenAPI spec for the Delivery API

**What to build:** The Delivery API publishes a machine-readable **OpenAPI 3** description of every `/api/v1` endpoint (read + write), so consumers can generate clients and explore the surface. Served from the app, kept in sync with the actual routes.

**Blocked by:** Best done after 01 (write-validation) so documented request schemas match enforced rules. Otherwise independent.

**Status:** ready-for-agent

- [ ] OpenAPI 3 document covering the current `/api/v1` surface: Pages, Posts (+taxonomy filters), Menus, Content-Entries, Search, and the write endpoints — with auth (Sanctum bearer), abilities, tenant behavior, pagination, and error shapes (`401/403/404/422/429`).
- [ ] Served at a stable path (e.g. `GET /api/v1/openapi.json`); decide whether generated from code/annotations or authored — prefer generation so it cannot drift. Document the choice.
- [ ] Optional: a docs UI (Swagger/Redoc) behind the same path family; must be self-contained if the public site enforces CSP.
- [ ] The spec is exercised by a test that asserts it is valid OpenAPI 3 and that every registered `/api/v1` route appears in it (drift guard).
- [ ] Pint + PHPStan max + full Pest suite green.
