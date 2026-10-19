<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\FieldSystem\Models\FieldSchema;
use Liberu\Cms\FieldSystem\Services\FieldSystemService;
use Liberu\Cms\FieldSystemFilament\Resources\FieldSchemaResource;

final class EditFieldSchema extends EditRecord
{
    #[\Override]
    protected static string $resource = FieldSchemaResource::class;

    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): FieldSchema
    {
        if (! $record instanceof FieldSchema) {
            throw ValidationException::withMessages(['schema' => 'The selected schema is invalid.']);
        }

        $key = $data['key'] ?? null;
        $name = $data['name'] ?? null;
        $teamId = $record->getAttribute('team_id');

        if (! is_string($key) || ! is_string($name) || ($teamId !== null && ! is_int($teamId) && ! is_string($teamId))) {
            throw ValidationException::withMessages(['schema' => 'The schema data is invalid.']);
        }

        return app(FieldSystemService::class)->saveSchema(
            $teamId === null ? null : (int) $teamId,
            $key,
            $name,
            is_array($data['fields'] ?? null) ? $data['fields'] : [],
            'Updated from the Filament Field System editor.',
        );
    }
}
