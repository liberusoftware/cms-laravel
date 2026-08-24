<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Hello\Models\Greeting;

/**
 * The Delivery API wire shape for a greeting.
 *
 * @mixin Greeting
 */
final class GreetingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'message' => $this->message,
        ];
    }
}
