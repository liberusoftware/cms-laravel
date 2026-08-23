# cms-posts

Blog posts with taxonomy for Liberu CMS, built on the content foundation.

Depends on the **Media** module (descriptor) and the **cms-content** foundation
(workflow + versioning). Featured images resolve through the **media contract**.

## Models

- **`Post`** — `HasWorkflow` + `HasRevisions`, implements `PublishableInterface`.
  Auto-unique slug (from title) and auto excerpt (from content) on save, a
  `is_featured` flag, an `author_id`, and `featuredMedia()` via the media contract.
- **`Category`** / **`Tag`** — slugged taxonomy, many-to-many with `Post`.

```php
$post = Post::create(['title' => 'Hello World', 'content' => '<p>…</p>']);
$post->slug;                       // "hello-world"
$post->excerpt;                    // first 40 words, tags stripped
$post->categories()->attach($id);
$post->tags()->attach($tagId);
$post->schedule(now()->addWeek()); // scheduled publish
```

## Repository

`PostRepositoryInterface` (module-internal): `find`, `findBySlug`, `published`,
`featured`, `byCategory(slug)`, `byTag(slug)` — all scoped to live posts.

## Config (`config/cms-posts.php`)

| Key | Default | Purpose |
|-----|---------|---------|
| `excerpt_words` | `40` | Word count for auto-generated excerpts. |

## Events

- **Emits:** via the content foundation — `ContentStateChanged`, `ContentPublished`.
- **Listens:** none.

## Notes

- Owns `cms_posts`, `cms_categories`, `cms_tags`, and the two pivots. The legacy
  host `Category`/`Tag`/`Collection` remain during the strangler migration — see
  `docs/OPEN-QUESTIONS.md`.
- `author_id` is a plain column; resolving it to a user awaits a user-directory
  contract (the access contract covers authorization, not identity lookup).
