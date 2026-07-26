# 02 — Quality gates enforced (repo-wide)

**What to build:** Make the quality gates real instead of scoped to the CMS packages: Pint clean across the whole repo, PHPStan analyzing `app/` at a frozen baseline, and Infection blocking with a calibrated score. Resolves OPEN-QUESTIONS #4, #5, #6.

**Blocked by:** 01 (hygiene) — do the big Pint reformat on a repo with the dead module wiring already gone.

**Status:** ready-for-agent

Pint (OQ#4):
- [ ] Run `vendor/bin/pint` repo-wide in a **single dedicated formatting commit** (no logic changes).
- [ ] Unscope the CI Pint gate so it covers the whole repo, not just `packages/liberu-cms` + `tests/*/Cms`.

PHPStan on `app/` (OQ#5):
- [ ] Add `app/` (and `database/`) to the PHPStan paths at a **baseline**: generate `phpstan-baseline.neon` to freeze existing debt, pick a starting level, wire into CI. Do NOT fix all findings now.
- [ ] Document the baseline + "climb the level over time" intent.

Infection blocking (OQ#6):
- [ ] Ensure CI has a coverage driver (pcov) so MSI can be measured.
- [ ] Calibrate `minMsi` / `minCoveredMsi` against a real CI run for the current Infection scope (cms-core / cms-contracts), then flip the step to **blocking** (`continue-on-error: false`).
- [ ] Widening Infection scope beyond cms-core/cms-contracts is explicitly out of scope here (future work).

- [ ] DoD: `pint --test` clean repo-wide in CI; PHPStan green at baseline including `app/`; Infection blocking in CI; full Pest suite green.
