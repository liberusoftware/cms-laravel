# Phase 7 — Extensibility (execution order)

Goal: turn the CMS's internal seams into a **public, documented, stable** extension
surface, and add the one missing primitive — **value-transforming hooks**. Non-goal:
runtime plugin install (extensions are Composer packages added at deploy time).

Foundation-first order; each ticket is a stacked feature branch (user pushes/PRs
bottom-up). Base: new branch off `feature/cms-auth-hardening` HEAD (`2538bf1`).

Full design: `docs/specs/phase-7-extensibility.md`. **Status: COMPLETE — all six tickets done** (branch `feature/cms-hooks`, not pushed).

| # | Ticket | Workstream | Status |
|---|--------|-----------|--------|
| 01 | HookBus foundation (tracer: block render) | Hooks | ✅ done (21d7d5a) |
| 02 | Remaining 3 core filter points | Hooks | ✅ done (676ad35) |
| 03 | `FieldTypeRegistry` (open closed seam) | Extension points | ✅ done (beed908) |
| 04 | Scaffolding generators | DX | ✅ done (735b96f) |
| 05 | Public contract catalog + stability | Public API | ✅ done (e81da7c) |
| 06 | Reference extension (`cms-hello`) + dev guide | Docs / proof | ✅ done |

Deferred to a later increment: extension **settings surface**, **version-compat
declaration**, **webhooks**. After Phase 7: Phase 6.5 (Performance / Observability /
Reliability) remains outstanding.

## Conventions carried from Phase 6

- Windows/Herd: composer ops via PowerShell, `--ignore-platform-reqs` when needed.
- No pgsql/mysql PDO locally → verify **full suite green on SQLite**; MySQL + Postgres
  legs run on push via the ticket-03 (Phase 6) CI matrix.
- `php artisan test` OOMs (forked runner reads php.ini 128M) → run
  `php -d memory_limit=-1 vendor/bin/pest`. PHPStan local: `--memory-limit=1G`.
- Every ticket ends: Pint clean · PHPStan **max** clean · arch-test green · full
  suite green. No Claude attribution in commits.
