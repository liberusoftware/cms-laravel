<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Core\Support\ApiPagination;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;
use Liberu\Cms\Pages\Http\Resources\PageResource;
use Liberu\Cms\Pages\Models\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serves published Pages over the Delivery API. Reads go through the Pages
 * repository; the tenant global scope (driven by the request tenant context)
 * restricts every query to the token's Team, so a cross-tenant slug is simply
 * not found.
 */
final readonly class PageApiController
{
    public function __construct(private PageRepositoryInterface $pages) {}

    public function index(): AnonymousResourceCollection
    {
        return PageResource::collection(ApiPagination::fromArray($this->pages->published()));
    }

    public function show(string $slug): PageResource
    {
        $page = str_contains($slug, '/') ? $this->pages->findByPath($slug) : $this->pages->findBySlug($slug);

        if (! $page instanceof Page || ! $page->isLive()) {
            throw new NotFoundHttpException;
        }

        return new PageResource($page);
    }
}
