# cms-content

The content foundation for Liberu CMS: **editorial workflow** and **versioning**,
shared by every content module (Pages, Posts, custom types).

Like `cms-core`, this is a **foundation library**, not a toggleable module —
content modules depend on its traits directly. It depends only on `cms-contracts`
and the framework.

## Workflow (Part B §14)

`Draft → Review → Published → Archived`, plus scheduling (Published with a future
date). The `Workflow` service (bound to `WorkflowInterface`) owns the transition
policy; the `HasWorkflow` trait gives a model the behaviour:

```php
$page->submitForReview();
$page->publish();               // → ContentPublished + ContentStateChanged events
$page->schedule(now()->addDay()); // Published, but isLive() is false until then
$page->archive();

$page->workflowState();  // WorkflowState enum
$page->isPublished();    // in Published state
$page->isLive();         // Published AND publish date reached
```

Illegal transitions throw `WorkflowExceptionInterface`. The model needs `status`
and `published_at` columns (the trait casts them).

## Versioning (Part B §14)

The `HasRevisions` trait records numbered snapshots and reverts to them:

```php
$page->recordRevision($userId);   // snapshot current fillable attributes
$page->revertTo(3);               // restore snapshot #3
$page->revisions();               // MorphMany history
```

Snapshots live in `cms_revisions` (polymorphic). Override
`revisionableAttributes()` to control what is captured.

## Events (in `cms-contracts`)

- **Emits:** `Content\ContentStateChanged`, `Content\ContentPublished`.
- **Listens:** none.

## Public contracts

`WorkflowInterface`, `WorkflowState`, `PublishableInterface`, `RevisionInterface`,
`WorkflowExceptionInterface` — all in `cms-contracts`.
