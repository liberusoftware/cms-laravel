<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemApi\Http\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class FieldSchemaController
{
    public function show(string $type): JsonResource
    {
        $contentType = ContentType::query()->where('key', $type)->first();
        if (! $contentType) {
            throw new NotFoundHttpException;
        }

        return new JsonResource([
            'key' => $contentType->key,
            'version' => $contentType->schema_version,
            'fields' => array_map(static fn ($field): array => $field->toArray(), $contentType->fieldDefinitions()),
        ]);
    }
}
