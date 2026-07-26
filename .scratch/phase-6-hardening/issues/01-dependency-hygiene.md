# 01 — Dependency hygiene

**What to build:** Remove the dead second module system and close the `composer audit` loophole, so the repo has exactly one module system and a blocking advisory gate. Resolves OPEN-QUESTIONS #1 and #7 / OWASP A05.2 + A06.

**Blocked by:** 00 (phpseclib bump — so the audit gate can go blocking without an immediate failure).

**Status:** ready-for-agent

- [ ] Remove `internachi/modular` from `composer.json` and its wiring: the `Modules\` autoload entry and the `app-modules/*` PHPUnit testsuite entry. Delete the empty `app-modules/` scaffold if unused.
- [ ] Confirm nothing references `internachi/modular` or `Modules\` (grep); full suite still green (the CMS uses the hand-rolled `Liberu\Cms\*` packages only).
- [ ] Make `composer audit` a **blocking** CI gate in `.github/workflows/security.yml`.
- [ ] Resolve `config.audit.block-insecure`: re-enable it if the tree is now clean, or document precisely why it stays `false` and what the reporting gate covers (update the STACK/OPEN-QUESTIONS note).
- [ ] DoD: no `internachi/modular` in the lockfile; one module system; `composer audit` clean and blocking in CI; full Pest suite green; Pint + PHPStan max clean.
