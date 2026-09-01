<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_image_processing_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('key', 120);
            $table->string('format', 20)->default('webp');
            $table->unsignedSmallInteger('quality')->default(82);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('fit', 20)->default('cover');
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('cms_image_processing_derivatives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('asset_key', 500);
            $table->foreignId('profile_id')->constrained('cms_image_processing_profiles')->cascadeOnDelete();
            $table->string('source_checksum', 128);
            $table->string('path', 500);
            $table->string('status', 30)->default('ready');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['profile_id', 'asset_key', 'source_checksum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_image_processing_derivatives');
        Schema::dropIfExists('cms_image_processing_profiles');
    }
};
