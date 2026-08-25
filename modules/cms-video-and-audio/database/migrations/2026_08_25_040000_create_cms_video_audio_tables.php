<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_media_assets', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('title');
            $table->string('kind');
            $table->string('source_type');
            $table->text('source_uri');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('bytes')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('stream_uri')->nullable();
            $table->string('poster_uri')->nullable();
            $table->string('status')->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->char('checksum', 64)->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'checksum']);
            $table->index(['kind', 'status']);
        });

        Schema::create('cms_media_tracks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained('cms_media_assets')->cascadeOnDelete();
            $table->string('track_type');
            $table->string('language', 16)->nullable();
            $table->string('label')->nullable();
            $table->text('uri')->nullable();
            $table->longText('content')->nullable();
            $table->decimal('start_seconds', 12, 3)->nullable();
            $table->decimal('end_seconds', 12, 3)->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('active')->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->index(['asset_id', 'track_type']);
        });

        Schema::create('cms_media_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained('cms_media_assets')->cascadeOnDelete();
            $table->string('idempotency_key');
            $table->string('adapter');
            $table->string('profile');
            $table->string('uri')->nullable();
            $table->string('status')->default('pending')->index();
            $table->unsignedBigInteger('bytes')->nullable();
            $table->json('metadata')->nullable();
            $table->text('failure_reason')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['asset_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_media_variants');
        Schema::dropIfExists('cms_media_tracks');
        Schema::dropIfExists('cms_media_assets');
    }
};
