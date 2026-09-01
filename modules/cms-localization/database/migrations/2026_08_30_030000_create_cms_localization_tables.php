<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_localization_locales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('locale', 35);
            $table->string('fallback_locale', 35)->nullable();
            $table->string('direction', 3)->default('ltr');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'locale']);
        });
        Schema::create('cms_localization_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('source_type', 120);
            $table->string('source_key', 255);
            $table->string('field', 120);
            $table->string('locale', 35);
            $table->text('value');
            $table->string('localized_slug', 240)->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'source_type', 'source_key', 'field', 'locale']);
            $table->index(['team_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_localization_variants');
        Schema::dropIfExists('cms_localization_locales');
    }
};
