<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Http\Concerns\WritesWorkflowContent;
use Liberu\Cms\Posts\Contracts\PostRepositoryInterface;
use Liberu\Cms\Posts\Http\Requests\StorePostRequest;
use Liberu\Cms\Posts\Http\Requests\UpdatePostRequest;
use Liberu\Cms\Posts\Http\Resources\PostResource;
use Liberu\Cms\Posts\Models\Category;
use Liberu\Cms\Posts\Models\Post;
use Liberu\Cms\Posts\Models\Tag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles Post writes on the Delivery API. Requires the `content:write` ability.
 * Category and tag ids are synced through tenant-scoped queries, so a foreign
 * tenant's taxonomy id is silently dropped rather than attached.
 */
final readonly class PostWriteController
{
    use WritesWorkflowContent;

    public function __construct(private PostRepositoryInterface $posts) {}

    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $status = $this->pullStatus($data);
        $categories = $this->pullIds($data, 'categories');
        $tags = $this->pullIds($data, 'tags');

        $post = Post::create($data + ['status' => WorkflowState::Draft->value]);

        $this->syncTaxonomy($post, $categories, $tags);

        if ($status instanceof WorkflowState && $this->shouldTransition($post->workflowState(), $status)) {
            $post->transitionTo($status);
        }

        return PostResource::make($post->refresh()->load('categories', 'tags'))->response()->setStatusCode(201);
    }

    public function update(UpdatePostRequest $request, int $id): PostResource
    {
        $post = $this->posts->find($id);

        if (! $post instanceof Post) {
            throw new NotFoundHttpException;
        }

        $data = $request->validated();
        $status = $this->pullStatus($data);
        $categories = $this->pullIds($data, 'categories');
        $tags = $this->pullIds($data, 'tags');

        if ($data !== []) {
            $post->update($data);
        }

        $this->syncTaxonomy($post, $categories, $tags);

        if ($status instanceof WorkflowState && $this->shouldTransition($post->workflowState(), $status)) {
            $post->transitionTo($status);
        }

        return PostResource::make($post->refresh()->load('categories', 'tags'));
    }

    public function destroy(int $id): Response
    {
        $post = $this->posts->find($id);

        if (! $post instanceof Post) {
            throw new NotFoundHttpException;
        }

        $post->delete();

        return response()->noContent();
    }

    /**
     * Extract a list of integer ids for a taxonomy key, or null when the key is
     * absent (meaning "leave the relation untouched").
     *
     * @param  array<string, mixed>  $data
     * @return list<int>|null
     */
    private function pullIds(array &$data, string $key): ?array
    {
        if (! array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];
        unset($data[$key]);

        if (! is_array($value)) {
            return [];
        }

        $ids = [];

        foreach ($value as $id) {
            if (is_int($id) || (is_string($id) && is_numeric($id))) {
                $ids[] = (int) $id;
            }
        }

        return $ids;
    }

    /**
     * @param  list<int>|null  $categoryIds
     * @param  list<int>|null  $tagIds
     */
    private function syncTaxonomy(Post $post, ?array $categoryIds, ?array $tagIds): void
    {
        if ($categoryIds !== null) {
            $post->categories()->sync(Category::query()->whereIn('id', $categoryIds)->pluck('id')->all());
        }

        if ($tagIds !== null) {
            $post->tags()->sync(Tag::query()->whereIn('id', $tagIds)->pluck('id')->all());
        }
    }
}
