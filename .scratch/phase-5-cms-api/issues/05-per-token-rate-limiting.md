# 05 — Per-token rate limiting

**What to build:** The Delivery API throttles each consumer by their Delivery token, so a single token that exceeds its budget gets a `429` with a `Retry-After` header while other tenants are unaffected.

**Blocked by:** 01 — foundation + Pages (needs the `/api/v1` group and token auth). Runs in parallel with 02–04 — it operates at the group level.

**Status:** ready-for-agent

- [ ] Per-token throttle applied to the `/api/v1` group, keyed by the authenticated token; default from `config('cms-api.rate_limit')`; per-IP fallback for unauthenticated paths.
- [ ] Exceeding the limit returns `429` with a `Retry-After` header.
- [ ] The limit is configurable, so a specific tenant's budget can be raised later without code changes.
- [ ] Feature tests: a token over its budget gets `429` + `Retry-After`; distinct tokens are throttled independently (one tenant hitting the limit does not throttle another).
- [ ] Pint + PHPStan max + full Pest suite green.
