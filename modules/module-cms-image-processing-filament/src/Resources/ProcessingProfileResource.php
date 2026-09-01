<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ImageProcessing\Models\ProcessingProfile;
use Liberu\Cms\ImageProcessingFilament\Resources\Pages\ListProcessingProfiles;

final class ProcessingProfileResource extends Resource
{
    #[\Override]
    protected static ?string $model = ProcessingProfile::class;

    #[\Override]
    protected static ?string $slug = 'cms-image-processing-profiles';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->maxLength(120), Select::make('format')->options(['jpg' => 'JPEG', 'png' => 'PNG', 'webp' => 'WebP', 'avif' => 'AVIF'])->required(), TextInput::make('quality')->numeric()->minValue(1)->maxValue(100)->required(), TextInput::make('width')->numeric()->minValue(1)->maxValue(10000), TextInput::make('height')->numeric()->minValue(1)->maxValue(10000), Select::make('fit')->options(['cover' => 'Cover', 'contain' => 'Contain', 'crop' => 'Crop', 'inside' => 'Inside'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable()->sortable(), TextColumn::make('format')->badge(), TextColumn::make('quality'), TextColumn::make('width'), TextColumn::make('height'), TextColumn::make('fit')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListProcessingProfiles::route('/')];
    }
}
