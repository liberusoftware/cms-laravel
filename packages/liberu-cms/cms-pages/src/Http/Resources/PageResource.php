<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Content\Support\HtmlSanitizer;
use Liberu\Cms\Core\Http\Concerns\EmbedsFeaturedMedia;
use Liberu\Cms\Pages\Models\Page;

/**
 * The Delivery API wire shape for a Page. Internal columns (team_id, workflow
 * internals, timestamps) are omitted; `content` is sanitised HTML and the
 * featured image is embedded as URL + alt text.
 *
 * @mixin Page
 */
final class PageResource extends JsonResource
{
    use EmbedsFeaturedMedia;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'template' => $this->template,
            'excerpt' => $this->excerpt,
            'content' => app(HtmlSanitizer::class)->sanitize($this->content),
            'featured_media' => $this->featuredMediaPayload($this->featuredMedia()),
            'published_at' => $this->publishedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
