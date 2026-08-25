<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ListingQueryService;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ViewDefinitionQuery;
use Liberu\Cms\ViewsAndQueryBuilderApi\Http\Resources\ListingRecordResource;
use Liberu\Cms\ViewsAndQueryBuilderApi\Http\Resources\ViewDefinitionResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ViewsAndQueryBuilderController
{
    public function __construct(private ViewDefinitionQuery $views, private ListingQueryService $listings) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return ViewDefinitionResource::collection($this->views->publishedList((int) $request->integer('per_page', 15), (string) $request->string('search')));
    }

    public function show(string $slug): ViewDefinitionResource
    {
        $view = $this->views->findPublished($slug);
        if (! $view) {
            throw new NotFoundHttpException;
        }

        return new ViewDefinitionResource($view);
    }

    public function execute(Request $request, string $slug): AnonymousResourceCollection
    {
        $view = $this->views->findPublished($slug);
        if (! $view) {
            throw new NotFoundHttpException;
        }

        return ListingRecordResource::collection($this->listings->execute($view, (int) $request->integer('per_page', 15), $request->array('filters')));
    }
}
