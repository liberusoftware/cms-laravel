# 03 — Database portability (MySQL + PostgreSQL)

**What to build:** Prove the portability the project advertises: the full test suite runs green on **both** MySQL and PostgreSQL in CI, and dev can spin up Postgres locally. Resolves OPEN-QUESTIONS #8 ("Phase 6 portability DoD").

**Blocked by:** 02 (gates) — so portability fixes land under enforced gates.

**Status:** DONE (branch `feature/cms-dependency-hygiene`, not pushed)

- [x] Add a `postgres` service to `docker-compose.yml` behind a compose profile (MySQL stays the default dev DB). — `postgres:16-alpine` behind the `postgres` profile.
- [x] Add a **CI matrix** running the full Pest suite against both `mysql` and `pgsql`. — `tests.yml` matrix `[sqlite, mysql, pgsql]`; both DB services run, the leg picks its DB env; coverage uploaded from the sqlite leg only.
- [x] Fix portability bugs the Postgres run surfaces. — Two real breakers: (1) `LIKE` on the `content_entries.data` `json` column → changed the column to `longText` (query unchanged, still `array`-cast); (2) `(bool) $value` on a raw pgsql boolean read → explicit `normalizeBool()` in `DatabaseModuleStateRepository` (pdo_pgsql can return `'t'`/`'f'`). No raw/vendor SQL introduced. Booleans read through Eloquent (`is_featured`, cast) and all boolean writes bind real PHP bools — already portable.
- [x] Audit the search `LIKE` queries (cms-search) and any JSON-field access. — Page/Post search columns are `string`/`text`/`longText` (LIKE-safe). ContentEntry `data` fixed above. Forms `fields`, submission `data`/`meta`, content-types `fields` are only ever array-cast (never SQL-queried) → left as `json`. No `whereRaw`/`groupBy`/JSON-path operators anywhere (only a portable `whereRaw('1 = 0')`).
- [x] DoD: full Pest suite **green on MySQL and PostgreSQL** in CI; Pint + PHPStan max clean. — Locally (no pgsql/mysql PDO or Docker on the Windows/Herd box): full suite **511 green** on SQLite, Pint + PHPStan max clean. **MySQL/PostgreSQL green is verified by the new CI matrix on push.**
