<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Widget;

/**
 * A dashboard/sidebar/footer widget that renders itself to HTML.
 *
 * @api This interface is part of the public extension API.
 */
interface WidgetInterface
{
    public function key(): string;

    public function title(): string;

    public function area(): WidgetArea;

    /**
     * Sort order within its area (ascending).
     */
    public function order(): int;

    public function render(): string;
}
