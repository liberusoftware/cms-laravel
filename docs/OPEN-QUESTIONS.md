# Open Questions

Ambiguities and deferred decisions, per Part A §9. Each has a chosen default so
work continues; revisit when the owning phase arrives.

## Architecture & dependencies

1. **`internachi/modular` — RESOLVED (Phase 6).** It was never used (Phase 0
   hand-rolls `packages/liberu-cms/*`). Removed the package from `composer.json`,
   the `Modules\` autoload entry, and the `app-modules/*` PHPUnit testsuite entry
   (the `app-modules/` scaffold never existed on disk). The repo now has exactly one
   module system. Removing it also dropped its `composer/composer` subtree, which
   cleared the transitive advisories behind Question 7.

2. **Filament Shield vs. `spatie/laravel-permission`. — RESOLVED (Phase 1).**
   They are layered, not competing: Spatie is the permission engine (stores
   roles/permissions, wires the gate); Shield is the Filament admin UI and
   policy/permission generator built on Spatie. The `cms-users` module exposes one
   contract, `AccessControlInterface`, implemented over the framework **gate**
   (which Spatie populates); Spatie is used only internally to materialise
   permissions (Golden Rule 2d). An architecture test now forbids any CMS package
   from importing the host `App\` namespace, so no module touches the users table.

3. **Existing host-app CMS code.** `app/Models` and `app/Filament` already contain
   Page, Menu, Collection, Category, Tag, etc., violating Golden Rule 1 (feature
   code in the host). **Default (agreed):** leave in place; migrate into modules in
   their proper phases (Pages/Posts/Media → Phase 2, Menu/Theme → Phase 3), keeping
   `main` green throughout.

   **Cutover progress (branch `feature/cms-legacy-cutover`).**
   - **Tenant coupling — RESOLVED.** `TenantModelResolverInterface` (cms-contracts)
     + `HasTenant` trait (cms-core) let module models scope to the host tenant via a
     `team()` relationship without importing `App\Models\Team`; the host binds
     `FilamentTenantResolver`. Applied to Page, Post, Category, Tag.
   - **Page — RETIRED.** The host `App\Models\Page`, its factory, and the legacy
     `pages` table are gone; `PageController`, public routes, `PageResource`, the
     menu-builder config, `PageSeeder`, and all tests now run on the module
     `Liberu\Cms\Pages\Models\Page` (`cms_pages`). Suite green.
   - **Still to do (now mechanical, unblocked):** retire host `Category`/`Tag`
     (→ cms-posts) and `Collection`/`CollectionItem`, `Menu`/`MenuItem`; relocate
     the Filament `PageResource` into the module (it currently lives in the host,
     repointed); and for any **existing deployment**, copy `pages` → `cms_pages`
     before the drop migration runs (fresh installs need nothing).

## Quality gates

4. **Pre-existing style debt.** `vendor/bin/pint --test` flags many existing
   `app/`, `config/`, `database/`, and `tests/` files. **Default:** the CI Pint gate
   is scoped to Phase 0 code (`packages/liberu-cms`, `tests/*/Cms`), which is clean.
   **Decision needed:** run repo-wide `pint` in a dedicated formatting commit.

5. **PHPStan scope — RESOLVED (Phase 6).** `app/` and `database/` are now in the
   analysed paths. The whole repo runs at **level max**; the CMS packages are
   already clean, so the 173 pre-existing host errors are frozen in
   `phpstan-baseline.neon`. CI is green including the host code. **Climb intent:**
   burn down the baseline over time (delete entries as the underlying errors are
   fixed); never regenerate it to absorb *new* errors — new host code must pass at
   max like everything else.

6. **Infection is non-blocking — two-step flip in progress (Phase 6).** Local Herd
   PHP 8.5 has no pcov/xdebug, so MSI still can't be measured on the dev box. CI now
   has `coverage: pcov` wired, and the Infection step is intentionally kept
   `continue-on-error: true` for one **calibration run** that prints the kernel's
   real MSI / Covered Code MSI (cms-core + cms-contracts scope). **Remaining step:**
   read those two numbers from the CI log, set `minMsi`/`minCoveredMsi` in
   `infection.json` just under them, and delete `continue-on-error: true` to make the
   gate blocking. Widening the Infection scope beyond the kernel is deferred.

## Security

7. **`audit.block-insecure` — RESOLVED (Phase 6): now `true`.** The advisories that
   forced it `false` came from the `composer/composer` subtree pulled in by the
   unused `internachi/modular` (Question 1); removing that package cleared them, and
   phpseclib was bumped to 3.0.55 (CVE-2026-55599). `composer audit` is now a
   **blocking** job in `.github/workflows/security.yml`, and `block-insecure: true`
   enforces the same gate on every local `composer update`. Re-audit if a future
   advisory appears (bump the affected dep rather than reopening the loophole).

## Dev environment

8. **PostgreSQL portability — RESOLVED (Phase 6, ticket 03).** A `postgres`
   service now sits behind a `postgres` compose profile (MySQL stays the default
   dev DB), and `.github/workflows/tests.yml` runs the full Pest suite across a
   `sqlite`/`mysql`/`pgsql` matrix — the portability DoD. Two engine-specific
   bugs the Postgres leg would surface were fixed: `content_entries.data` is now
   `longText` (Postgres rejects `LIKE` against a `json` column, which the search
   query needs), and `DatabaseModuleStateRepository` normalises boolean reads
   explicitly (pdo_pgsql can return `'t'`/`'f'`, and `(bool) 'f'` is `true`).

## Framework

9. **Pest 5 vs Pest 4.** Guidelines mention Pest 4; the repo installs Pest 5.x-dev.
   **Default:** build on Pest 5 (installed). No action expected.
