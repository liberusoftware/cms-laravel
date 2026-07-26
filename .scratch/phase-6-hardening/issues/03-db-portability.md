# 03 — Database portability (MySQL + PostgreSQL)

**What to build:** Prove the portability the project advertises: the full test suite runs green on **both** MySQL and PostgreSQL in CI, and dev can spin up Postgres locally. Resolves OPEN-QUESTIONS #8 ("Phase 6 portability DoD").

**Blocked by:** 02 (gates) — so portability fixes land under enforced gates.

**Status:** ready-for-agent

- [ ] Add a `postgres` service to `docker-compose.yml` behind a compose profile (MySQL stays the default dev DB).
- [ ] Add a **CI matrix** running the full Pest suite against both `mysql` and `pgsql`.
- [ ] Fix portability bugs the Postgres run surfaces (case-sensitivity, `group by`/aggregate strictness, JSON column operators, boolean casting, `LIKE` vs `ILIKE`, auto-increment/sequence assumptions). Keep all DB access through the schema/query builder — no vendor SQL (per Golden Rules).
- [ ] Audit the search `LIKE` queries (cms-search) and any JSON-field access (ContentEntry `data`, forms `fields`) for cross-engine behavior.
- [ ] DoD: full Pest suite **green on MySQL and PostgreSQL** in CI; Pint + PHPStan max clean.
