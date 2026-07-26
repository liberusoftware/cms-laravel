# Phase 6 — Hardening (execution order)

Goal: turn "16 packages that pass their own tests" into a **real deployable product**.
Foundation-first order; each ticket is a stacked feature branch (user pushes/PRs).
**Phase 5 close-out (`.scratch/phase-5-closeout/`) lands first.**

| # | Ticket | Workstream | Blocked by |
|---|--------|-----------|-----------|
| 00 | phpseclib CVE bump | Hygiene | — (first) |
| 01 | Dependency hygiene | Hygiene | 00 |
| 02 | Quality gates enforced | Gates | 01 |
| 03 | DB portability (MySQL + Postgres) | Portability | 02 |
| 04 | Module-owned policies | Security | 03 |
| 05 | `cms-audit` module | Security | 04 |
| 06 | Production config + headers | Security | 04 |
| 07 | Auth hardening | Security | 04 |

Deferred to a later "Phase 6.5": Performance, Observability, Reliability. Then Phase 7 (Extensibility).
