# 04 — Content-Entries endpoint

**What to build:** A consumer can read published Content-Entries of a given custom type over the Delivery API — `GET /api/v1/content-entries?type={typeKey}` and `GET /api/v1/content-entries/{slug}` — with each entry's typed fields exposed as structured JSON, so a frontend consumes dynamically-defined content without knowing the schema at build time.

**Blocked by:** 01 — foundation + Pages (needs the `/api/v1` group, auth, tenant context, and the resource registry).

**Status:** ready-for-agent

- [ ] `cms-content-types` registers its Content-Entry API controller + Eloquent Resource into the registry (same guarded pattern).
- [ ] `GET /api/v1/content-entries?type={typeKey}` returns published entries of that type (reusing `ofType` + `published`), paginated, `per_page` capped.
- [ ] `GET /api/v1/content-entries/{slug}` returns one published entry (reusing `findBySlug`).
- [ ] The Resource exposes the entry's typed fields as structured JSON keyed by field name (from the ContentType's field schema); any HTML/rich field sanitized via `HtmlSanitizer`; internal columns omitted.
- [ ] ContentType definitions are NOT exposed as an endpoint (schemas are global/shared) — only entries are delivered.
- [ ] Feature tests: published-only visibility; type filter returns the right subset; typed fields present + correctly shaped; cross-tenant entry → `404` (isolation); unauth `401`.
- [ ] Pint + PHPStan max + full Pest suite green.
