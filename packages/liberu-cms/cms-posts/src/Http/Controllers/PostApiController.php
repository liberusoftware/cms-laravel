<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Core\Support\ApiPagination;
use Liberu\Cms\Posts\Contracts\PostRepositoryInterface;
use Liberu\Cms\Posts\Http\Resources\PostResource;
use Liberu\Cms\Posts\Models\Post;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serves published Posts over the Delivery API, newest first, optionally
 * filtered by a category or tag slug. The tenant global scope restricts every
 * query to the token's Team.
 */
final readonly class PostApiController
{
    public function __construct(private PostRepositoryInterface $posts) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $category = $request->query('category');
        $tag = $request->query('tag');

        $posts = match (true) {
            is_string($category) && $category !== '' => $this->posts->byCategory($category),
            is_string($tag) && $tag !== '' => $this->posts->byTag($tag),
            default => $this->posts->published(),
        };

        $posts = (new Collection($posts))->loadMissing(['categories', 'tags']);

        return PostResource::collection(ApiPagination::fromArray($posts->all()));
    }

    public function show(string $slug): PostResource
    {
        $post = $this->posts->findBySlug($slug);

        if (! $post instanceof Post || ! $post->isLive()) {
            throw new NotFoundHttpException;
        }

        $post->loadMissing(['categories', 'tags']);

        return new PostResource($post);
    }
}
