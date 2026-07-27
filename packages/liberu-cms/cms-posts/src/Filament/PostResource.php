<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Hooks\Filters\AdminFormSchemaFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Posts\Filament\Pages\ListPosts;
use Liberu\Cms\Posts\Models\Post;
use UnitEnum;

/**
 * Admin surface for the Posts module: blog posts with taxonomy, a featured
 * flag, and the editorial workflow. Owned by the module.
 */
final class PostResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $slug = 'cms-posts';

    protected static ?string $navigationLabel = 'Posts';

    protected static ?string $recordTitleAttribute = 'title';

    protected static function cmsPermissionKey(): string
    {
        return 'posts';
    }

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
                    Select::make('status')
                        ->options(WorkflowState::options())
                        ->default(WorkflowState::Draft->value)
                        ->required(),
                    Select::make('categories')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload(),
                    Select::make('tags')
                        ->relationship('tags', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload(),
                    Toggle::make('is_featured')
                        ->label('Featured')
                        ->columnSpanFull(),
                    Textarea::make('excerpt')
                        ->rows(2)
                        ->helperText('Leave blank to generate from the content.')
                        ->columnSpanFull(),
                    Textarea::make('content')
                        ->rows(10)
                        ->columnSpanFull(),
                ]),
        ];

        return $schema->components(
            app(HookBusInterface::class)->apply(new AdminFormSchemaFilter($components, 'posts'))->components
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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
    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
        ];
    }
}
