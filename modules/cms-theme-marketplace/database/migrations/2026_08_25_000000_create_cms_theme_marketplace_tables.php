<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_marketplace_themes', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->string('version');
            $table->string('author');
            $table->text('description')->nullable();
            $table->json('manifest');
            $table->json('compatibility')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('license');
            $table->string('parent_key')->nullable();
            $table->string('status')->default('draft');
            $table->string('security_status')->default('pending');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['key', 'version', 'team_id'], 'cms_marketplace_theme_version');
            $table->index(['status', 'security_status']);
            $table->index('team_id');
        });
        Schema::create('cms_theme_installations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('theme_id')->constrained('cms_marketplace_themes')->cascadeOnDelete();
            $table->string('site_key');
            $table->string('installed_version');
            $table->string('status')->default('installed');
            $table->timestamp('installed_at')->nullable();
            $table->string('updated_at_version')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['site_key', 'theme_id', 'team_id'], 'cms_theme_installation_identity');
            $table->index('team_id');
        });
        Schema::create('cms_theme_ratings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('theme_id')->constrained('cms_marketplace_themes')->cascadeOnDelete();
            $table->string('reviewer_type');
            $table->string('reviewer_id');
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['theme_id', 'reviewer_type', 'reviewer_id', 'team_id'], 'cms_theme_rating_identity');
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_theme_ratings');
        Schema::dropIfExists('cms_theme_installations');
        Schema::dropIfExists('cms_marketplace_themes');
    }
};
