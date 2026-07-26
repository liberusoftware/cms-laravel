# Phase 5 close-out: write validation, preview links, OpenAPI, token UI

Completes the four deferred Phase 5 items so the Delivery phase is fully closed
before Phase 6 (Hardening). Stacked on `feature/cms-admin-forms-notifications`.

Compare URL:
https://github.com/ducz07/cms-laravel/compare/feature/cms-admin-forms-notifications...feature/cms-content-entry-validation?expand=1

## Changes
- **ContentEntry write validation** (`023c177`) — API writes validate `data`
  against the content type's schema (reuses `SchemaValidator`); 422 with
  `data.<field>` errors; unknown fields rejected; updates validate against the
  persisted (tenant-scoped) type so a spoofed `content_type_id` can't bypass.
- **Signed preview links** (`46c7b0e`) — `GET /api/v1/preview/{type}/{id}`,
  unauthenticated but signed + expiring, reveals a single draft-inclusive item;
  tenant-scoped; `cms-api:preview-link` command. `PreviewRegistry` +
  `PreviewableSource` contracts; pages/posts/content-entries register sources.
- **OpenAPI 3 spec** (`9264019`) — public `GET /api/v1/openapi.json`, generated
  from the live router so paths can't drift; covers auth, abilities, params, and
  401/403/404/422/429; drift-guard test.
- **API token-management UI** (`414dc1d`) — Filament `ApiTokenResource` to mint
  (reveal-once), scope, list, and revoke Team tokens; gated by a new
  `api-tokens.manage` permission; tenant-scoped.

## Quality
- Full suite **511 passing** (serial); Pint + PHPStan max clean.
- No Phase 6 work included.
