# 02 — Preview / draft tokens

**What to build:** An editor can share a signed, expiring preview link that renders a **single unpublished** piece of content (Page/Post/ContentEntry) without authenticating — so drafts can be reviewed before publish — while the token leaks nothing beyond the one item it names and expires automatically.

**Blocked by:** None (web/API preview path). Independent of 01/03/04.

**Status:** ready-for-agent

- [ ] Signed, expiring preview tokens (Laravel signed URLs or Sanctum short-lived ability tokens — choose and document) scoped to a **single** content id + type + tenant.
- [ ] A preview route/endpoint resolves the token and returns the named item **regardless of workflow state** (draft/review included), tenant-scoped, without exposing other items.
- [ ] Token carries an expiry; expired or tampered token → `403`/`404` (do not leak existence across tenants).
- [ ] Preview never bypasses tenant isolation and never returns a different item than the one it was minted for.
- [ ] A way to mint a preview token (artisan command and/or a Filament "Preview" action on the resource).
- [ ] Behavior guarantees (feature tests): valid token → draft visible; expired token → denied; tampered token → denied; cross-tenant token → denied; published-only endpoints still hide drafts.
- [ ] Pint + PHPStan max + full Pest suite green.
