<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Menus\Http\Resources\MenuResource;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;
use Liberu\Cms\Menus\Services\MenuService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class NavigationApiController
{
    public function __construct(private MenuService $menus) {}

    public function index(Request $request): JsonResponse
    {
        $variant = (string) $request->query('variant', 'default');

        return response()->json(['data' => Menu::query()->where('variant', $variant)->orderBy('name')->get()->map(
            fn (Menu $menu): array => ['id' => (string) $menu->getKey(), 'type' => 'cms-navigation', 'attributes' => ['name' => $menu->name, 'location' => $menu->location, 'variant' => $menu->variant]],
        )->values()->all()]);
    }

    public function show(int $id): MenuResource
    {
        return new MenuResource($this->menu($id));
    }

    public function store(Request $request): JsonResponse
    {
        $menu = $this->menus->createMenu($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'variant' => ['sometimes', 'string', 'max:255'],
            'settings' => ['sometimes', 'array'],
        ]));

        return new MenuResource($menu->refresh())->response()->setStatusCode(201);
    }

    public function update(Request $request, int $id): MenuResource
    {
        return new MenuResource($this->menus->updateMenu($this->menu($id), $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'variant' => ['sometimes', 'string', 'max:255'],
            'settings' => ['sometimes', 'array'],
        ])));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->menu($id)->delete();

        return response()->json(status: 204);
    }

    public function storeItem(Request $request, int $id): JsonResponse
    {
        $item = $this->menus->saveItem($this->menu($id), $this->itemData($request));

        return response()->json(['data' => $this->itemPayload($item)], 201);
    }

    public function updateItem(Request $request, int $id, int $item): JsonResponse
    {
        $menu = $this->menu($id);
        $record = MenuItem::query()->where('menu_id', $menu->getKey())->find($item);
        if (! $record instanceof MenuItem) {
            throw new NotFoundHttpException;
        }

        return response()->json(['data' => $this->itemPayload($this->menus->saveItem($menu, $this->itemData($request), $record))]);
    }

    public function destroyItem(int $id, int $item): JsonResponse
    {
        $menu = $this->menu($id);
        $record = MenuItem::query()->where('menu_id', $menu->getKey())->find($item);
        if (! $record instanceof MenuItem) {
            throw new NotFoundHttpException;
        }
        $this->menus->deleteItem($record);

        return response()->json(status: 204);
    }

    private function menu(int $id): Menu
    {
        $menu = Menu::query()->find($id);
        if (! $menu instanceof Menu) {
            throw new NotFoundHttpException;
        }

        return $menu;
    }

    /** @return array<string, mixed> */
    private function itemData(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'link_type' => ['sometimes', 'string', 'in:content,custom,system'],
            'content_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'system_route' => ['sometimes', 'nullable', 'string', 'max:255'],
            'parent_id' => ['sometimes', 'nullable', 'integer'],
            'sort' => ['sometimes', 'integer', 'min:0'],
            'permission' => ['sometimes', 'nullable', 'string', 'max:255'],
            'visibility' => ['sometimes', 'array'],
            'active' => ['sometimes', 'boolean'],
        ]);
    }

    /** @return array<string, mixed> */
    private function itemPayload(MenuItem $item): array
    {
        return $item->only(['id', 'menu_id', 'parent_id', 'label', 'url', 'link_type', 'content_id', 'system_route', 'sort', 'permission', 'visibility', 'active']);
    }
}
