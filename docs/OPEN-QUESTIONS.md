# Open Questions

Ambiguities and deferred decisions, per Part A §9. Each has a chosen default so
work continues; revisit when the owning phase arrives.

## Architecture & dependencies

1. **`internachi/modular` is now unused.** Phase 0 hand-rolls
   `packages/liberu-cms/*` (project decision). The `internachi/modular` package and
   its `app-modules/` autoload/testsuite wiring remain installed but unused.
   **Default:** leave installed for now. **Decision needed:** remove it (and the
   `Modules\` autoload + `app-modules/*` phpunit entry) to avoid two module systems.

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

5. **PHPStan scope.** Max level runs on `packages/liberu-cms/*/src` only. **Decision
   needed:** raise the pre-existing `app/` to a baseline (e.g. level 5) then climb.

6. **Infection is non-blocking.** Local Herd PHP has no pcov/xdebug, so MSI could not
   be measured here; the CI step runs with `continue-on-error: true`. **Decision
   needed:** calibrate `minMsi`/`minCoveredMsi` against a real CI run, then make it
   blocking.

## Security

7. **`audit.block-insecure: false`.** Enables the module `composer update` workflow
   on this locked dev stack (upstream transitive advisories on guzzle, psr7,
   composer/composer). `composer install` (CI) does not re-solve and is unaffected.
   **Mitigation:** keep `composer audit` in `.github/workflows/security.yml` as the
   reporting gate. **Decision needed:** revisit once upstream deps clear the
   advisories.

## Dev environment

8. **PostgreSQL not in `docker-compose.yml`.** Compose ships MySQL + Redis +
   Meilisearch + Mailpit. Database code is written portably (schema/query builder),
   but the dev stack only spins up MySQL. **Default:** MySQL for dev. **Decision
   needed:** add a `postgres` service behind a compose profile and run the suite
   against both in CI (Phase 6 portability DoD).

## Framework

9. **Pest 5 vs Pest 4.** Guidelines mention Pest 4; the repo installs Pest 5.x-dev.
   **Default:** build on Pest 5 (installed). No action expected.
