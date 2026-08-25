<?php

declare(strict_types=1);

namespace Liberu\Cms\PagesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Pages\Models\PageRedirect;
use Liberu\Cms\Pages\Services\PageRoutingService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class PageRoutingController
{
    public function __construct(private PageRoutingService $routing) {}

    public function alias(Request $request, int $id): JsonResponse
    {
        $page = Page::query()->find($id);
        if (! $page instanceof Page) {
            throw new NotFoundHttpException;
        }

        $alias = $this->routing->addAlias($page, (string) $request->input('path'));

        return response()->json(['data' => ['id' => $alias->id, 'page_id' => $alias->page_id, 'path' => $alias->path]], 201);
    }

    public function redirect(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from_path' => ['required', 'string', 'max:255'],
            'to_path' => ['required', 'string', 'max:255'],
            'status_code' => ['sometimes', 'integer'],
            'active' => ['sometimes', 'boolean'],
        ]);
        $redirect = $this->routing->createRedirect($data);

        return response()->json(['data' => $redirect->only(['id', 'from_path', 'to_path', 'status_code', 'active'])], 201);
    }

    public function deleteRedirect(int $id): JsonResponse
    {
        $redirect = PageRedirect::query()->find($id);
        if (! $redirect instanceof PageRedirect) {
            throw new NotFoundHttpException;
        }

        $this->routing->deleteRedirect($redirect);

        return response()->json(status: 204);
    }
}
