<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\Api\OpenApi\OpenApiGenerator;

/**
 * Serves the machine-readable OpenAPI 3 description of the Delivery API. The
 * document is generated from the live router, so it always reflects the
 * currently registered routes. Public: a consumer fetches it without a token to
 * generate a client or explore the surface.
 */
final readonly class OpenApiController
{
    public function __construct(private OpenApiGenerator $generator) {}

    public function __invoke(): JsonResponse
    {
        return response()->json($this->generator->generate());
    }
}
