<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptionsFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\NotificationsAndSubscriptions\Models\Subscription;

final class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $slug = 'cms-subscriptions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('subscriber_type')->required(), TextInput::make('subscriber_id')->required(), TextInput::make('subject_type')->required(), TextInput::make('subject_id')->required(), Select::make('frequency')->options(['instant' => 'Instant', 'daily' => 'Daily', 'weekly' => 'Weekly'])->required(), Select::make('channels')->multiple()->options(['mail' => 'Mail', 'web' => 'Web', 'push' => 'Push', 'log' => 'Log'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subscriber_id')->searchable(), TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('frequency')->badge(), TextColumn::make('active')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListSubscriptions::route('/')];
    }
}
