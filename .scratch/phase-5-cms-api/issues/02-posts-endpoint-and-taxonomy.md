# 02 — Posts endpoint + taxonomy

**What to build:** A consumer can list and read published Posts over the Delivery API, filtered by taxonomy — `GET /api/v1/posts` (paginated, newest-first), `GET /api/v1/posts/{slug}`, with `?category={slug}` and `?tag={slug}` filters — each Post carrying its categories, tags, and featured media inline.

**Blocked by:** 01 — foundation + Pages (needs the `/api/v1` group, auth, tenant context, and the resource registry).

**Status:** ready-for-agent

- [ ] `cms-posts` registers its API controller + Eloquent Resource into the registry (same guarded pattern as its Filament resource).
- [ ] `GET /api/v1/posts` returns published Posts paginated newest-first (reusing `PostRepositoryInterface::published`), `per_page` capped.
- [ ] `GET /api/v1/posts/{slug}` returns one published Post.
- [ ] `?category={slug}` and `?tag={slug}` filter the collection (reusing `byCategory` / `byTag`).
- [ ] Post Resource embeds categories + tags inline and featured media (URL + alt); `content` sanitized via `HtmlSanitizer`; internal columns omitted.
- [ ] Feature tests: published-only visibility; single/collection by tenant (isolation); taxonomy filters return the correct subset; sanitized content; unauth `401`; cross-tenant/unpublished `404`.
- [ ] Pint + PHPStan max + full Pest suite green.
