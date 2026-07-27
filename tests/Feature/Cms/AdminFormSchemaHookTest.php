<?php

declare(strict_types=1);

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Liberu\Cms\Contracts\Hooks\Filters\AdminFormSchemaFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Pages\Filament\PageResource;
use Liberu\Cms\Posts\Filament\PostResource;

function addSubtitleToPages(): void
{
    app(HookBusInterface::class)->listen(AdminFormSchemaFilter::class, function (AdminFormSchemaFilter $filter): void {
        if ($filter->resource === 'pages') {
            $filter->components[] = TextInput::make('subtitle');
        }
    });
}

/**
 * @return array<int, string>
 */
function topLevelFieldNames(Schema $schema): array
{
    return collect($schema->getComponents())
        ->filter(fn (object $component): bool => $component instanceof Field)
        ->map(fn (Field $field): string => $field->getName())
        ->values()
        ->all();
}

it('lets an extension add a field to a resource form via AdminFormSchemaFilter', function (): void {
    addSubtitleToPages();

    expect(topLevelFieldNames(PageResource::form(Schema::make())))->toContain('subtitle');
});

it('scopes the admin form hook to the targeted resource', function (): void {
    addSubtitleToPages();

    expect(topLevelFieldNames(PostResource::form(Schema::make())))->not->toContain('subtitle');
});

it('builds the page form unchanged when no admin hook is registered', function (): void {
    expect(topLevelFieldNames(PageResource::form(Schema::make())))->not->toContain('subtitle');
});
