<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Content\Support\HtmlSanitizer;
use Liberu\Cms\Core\Http\Concerns\EmbedsFeaturedMedia;
use Liberu\Cms\Core\Http\Concerns\FiltersApiResource;
use Liberu\Cms\Posts\Models\Category;
use Liberu\Cms\Posts\Models\Post;
use Liberu\Cms\Posts\Models\Tag;

/**
 * The Delivery API wire shape for a Post: sanitised HTML content, an embedded
 * featured image (URL + alt), and inline categories and tags so a consumer can
 * render taxonomy links without extra requests.
 *
 * @mixin Post
 */
final class PostResource extends JsonResource
{
    use EmbedsFeaturedMedia;
    use FiltersApiResource;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->withApiResourceFilter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => app(HtmlSanitizer::class)->sanitize($this->content),
            'featured_media' => $this->featuredMediaPayload($this->featuredMedia()),
            'categories' => $this->categories->map(fn (Category $category): array => [
                'name' => $category->name,
                'slug' => $category->slug,
            ])->values()->all(),
            'tags' => $this->tags->map(fn (Tag $tag): array => [
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])->values()->all(),
            'published_at' => $this->publishedAt()?->format(\DateTimeInterface::ATOM),
        ]);
    }
}
