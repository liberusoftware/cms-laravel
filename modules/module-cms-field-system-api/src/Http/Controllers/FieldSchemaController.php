<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemApi\Http\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ContentTypes\Queries\FieldSchemaQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class FieldSchemaController
{
    public function __construct(private FieldSchemaQuery $schemas) {}

    public function show(string $type): JsonResource
    {
        if (! preg_match('/^[a-z0-9][a-z0-9_-]{0,254}$/', $type)) {
            throw new NotFoundHttpException;
        }

        $schema = $this->schemas->find($type);

        if ($schema === null) {
            throw new NotFoundHttpException;
        }

        return new JsonResource($schema);
    }
}
