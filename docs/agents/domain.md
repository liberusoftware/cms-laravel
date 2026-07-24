# Domain Docs

How the engineering skills should consume this repo's domain documentation when exploring the codebase.

This is a **multi-context** repo: a modular CMS whose contexts live as separate Composer packages under `packages/liberu-cms/cms-*` (e.g. `cms-core`, `cms-pages`, `cms-posts`, `cms-media`, `cms-widgets`, `cms-admin`), each with its own `src/`.

## Before exploring, read these

- **`CONTEXT-MAP.md`** at the repo root — it points at one `CONTEXT.md` per context (per package). Read each one relevant to the topic.
- Per-package **`CONTEXT.md`** under `packages/liberu-cms/<package>/` for the context you're working in.
- **`docs/adr/`** at the repo root for system-wide decisions, plus `packages/liberu-cms/<package>/docs/adr/` for context-scoped decisions that touch the area you're about to work in.

If any of these files don't exist, **proceed silently**. Don't flag their absence; don't suggest creating them upfront. The `/domain-modeling` skill (reached via `/grill-with-docs` and `/improve-codebase-architecture`) creates them lazily when terms or decisions actually get resolved.

## File structure

Multi-context layout (this repo):

```
/
├── CONTEXT-MAP.md                       ← points at each package's CONTEXT.md
├── docs/adr/                            ← system-wide decisions
└── packages/liberu-cms/
    ├── cms-core/
    │   ├── CONTEXT.md
    │   ├── docs/adr/                    ← context-specific decisions
    │   └── src/
    ├── cms-pages/
    │   ├── CONTEXT.md
    │   ├── docs/adr/
    │   └── src/
    └── cms-posts/
        ├── CONTEXT.md
        ├── docs/adr/
        └── src/
```

## Use the glossary's vocabulary

When your output names a domain concept (in an issue title, a refactor proposal, a hypothesis, a test name), use the term as defined in the relevant `CONTEXT.md`. Don't drift to synonyms the glossary explicitly avoids.

If the concept you need isn't in the glossary yet, that's a signal — either you're inventing language the project doesn't use (reconsider) or there's a real gap (note it for `/domain-modeling`).

## Flag ADR conflicts

If your output contradicts an existing ADR, surface it explicitly rather than silently overriding:

> _Contradicts ADR-0007 (event-sourced orders) — but worth reopening because…_
