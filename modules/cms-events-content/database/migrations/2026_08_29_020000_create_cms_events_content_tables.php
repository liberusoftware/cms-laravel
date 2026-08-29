<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_event_speakers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('email')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->index('team_id');
        });
        Schema::create('cms_event_venues', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('timezone')->default('UTC');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->index('team_id');
        });
        Schema::create('cms_events', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->string('timezone')->default('UTC');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('archived_at')->nullable();
            $table->json('structured_data')->nullable();
            $table->unsignedBigInteger('venue_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['key', 'team_id']);
            $table->index(['status', 'starts_at']);
            $table->index('team_id');
        });
        Schema::create('cms_event_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('cms_events')->cascadeOnDelete();
            $table->string('key');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('room')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'key']);
            $table->index(['event_id', 'starts_at']);
        });
        Schema::create('cms_event_speaker_session', function (Blueprint $table): void {
            $table->foreignId('session_id')->constrained('cms_event_sessions')->cascadeOnDelete();
            $table->foreignId('speaker_id')->constrained('cms_event_speakers')->cascadeOnDelete();
            $table->primary(['session_id', 'speaker_id']);
        });
        Schema::create('cms_event_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('cms_events')->cascadeOnDelete();
            $table->string('provider');
            $table->string('external_key');
            $table->string('url')->nullable();
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_event_registrations');
        Schema::dropIfExists('cms_event_speaker_session');
        Schema::dropIfExists('cms_event_sessions');
        Schema::dropIfExists('cms_events');
        Schema::dropIfExists('cms_event_venues');
        Schema::dropIfExists('cms_event_speakers');
    }
};
