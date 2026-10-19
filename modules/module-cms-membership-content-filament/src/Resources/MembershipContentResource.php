<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContentFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\MembershipContent\Models\MembershipContent;

final class MembershipContentResource extends Resource
{
    #[\Override]
    protected static ?string $model = MembershipContent::class;

    #[\Override]
    protected static ?string $slug = 'cms-membership-content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(200),
            TextInput::make('subject_type')->required()->maxLength(120),
            TextInput::make('subject_key')->required()->maxLength(180),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required(),
            Textarea::make('description')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('subject_type')->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListMembershipContent::route('/')];
    }
}
