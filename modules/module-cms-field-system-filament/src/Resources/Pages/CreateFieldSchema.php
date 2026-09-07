<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\FieldSystem\Models\FieldSchema;
use Liberu\Cms\FieldSystem\Services\FieldSystemService;
use Liberu\Cms\FieldSystemFilament\Resources\FieldSchemaResource;

final class CreateFieldSchema extends CreateRecord
{
    #[\Override]
    protected static string $resource = FieldSchemaResource::class;

    #[\Override]
    protected function handleRecordCreation(array $data): FieldSchema
    {
        $key = $data['key'] ?? null;
        $name = $data['name'] ?? null;

        if (! is_string($key) || ! is_string($name)) {
            throw ValidationException::withMessages(['schema' => 'A schema key and name are required.']);
        }

        return app(FieldSystemService::class)->saveSchema(
            FieldSchemaResource::currentTeamId(),
            $key,
            $name,
            is_array($data['fields'] ?? null) ? $data['fields'] : [],
        );
    }
}
