<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Hooks\Filters\AdminFormSchemaFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Pages\Filament\Pages\ListPages;
use Liberu\Cms\Pages\Models\Page;
use UnitEnum;

/**
 * Admin surface for the Pages module: hierarchical pages with an editorial
 * workflow. Owned by the module (not the host) so the dependency direction
 * stays host → module; the Admin module never imports it directly.
 */
final class PageResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = Page::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-pages';

    #[\Override]
    protected static ?string $navigationLabel = 'Pages';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'title';

    protected static function cmsPermissionKey(): string
    {
        return 'pages';
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        $components = [
            Section::make()
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->maxLength(255)
                        ->helperText('Leave blank to generate from the title.'),
                    TextInput::make('template')
                        ->default('default')
                        ->required()
                        ->maxLength(255),
                    Select::make('status')
                        ->options(WorkflowState::options())
                        ->default(WorkflowState::Draft->value)
                        ->required(),
                    Select::make('parent_id')
                        ->relationship('parent', 'title')
                        ->searchable()
                        ->preload()
                        ->label('Parent page'),
                    Textarea::make('excerpt')
                        ->rows(2)
                        ->columnSpanFull(),
                    Textarea::make('content')
                        ->rows(10)
                        ->columnSpanFull(),
                ]),
        ];

        return $schema->components(
            app(HookBusInterface::class)->apply(new AdminFormSchemaFilter($components, 'pages'))->components
        );
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('template')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
        ];
    }
}
