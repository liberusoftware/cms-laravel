<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Http\Controllers;

use Liberu\Cms\Menus\Contracts\MenuRepositoryInterface;
use Liberu\Cms\Menus\Http\Resources\MenuResource;
use Liberu\Cms\Menus\Models\Menu;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serves a Menu by its navigation location over the Delivery API. Menus carry no
 * workflow state, so `forLocation` — tenant-scoped like every other read — is
 * the visibility seam.
 */
final readonly class MenuApiController
{
    public function __construct(private MenuRepositoryInterface $menus) {}

    public function show(string $location): MenuResource
    {
        $menu = $this->menus->forLocation($location);

        if (! $menu instanceof Menu) {
            throw new NotFoundHttpException;
        }

        return new MenuResource($menu);
    }
}
