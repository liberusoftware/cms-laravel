<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussionFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\CommentsAndDiscussion\Models\Comment;

final class CommentResource extends Resource
{
    #[\Override]
    protected static ?string $model = Comment::class;

    #[\Override]
    protected static ?string $slug = 'cms-comments-and-discussion';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Textarea::make('body')->required()->maxLength(10000), Select::make('status')->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'spam' => 'Spam', 'removed' => 'Removed'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('commentable_type')->label('Target'), TextColumn::make('commentable_id'), TextColumn::make('body')->limit(80), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()->sortable()])->defaultSort('created_at', 'desc');
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListComments::route('/')];
    }
}
