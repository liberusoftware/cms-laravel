<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_theme_bindings', function (Blueprint $table): void {
            $table->id();
            $table->string('site_key');
            $table->string('channel_key')->nullable();
            $table->string('theme_key');
            $table->string('fallback_theme_key')->default('default');
            $table->string('preview_token')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['site_key', 'channel_key', 'team_id'], 'cms_theme_binding_identity');
            $table->index('team_id');
        });
        Schema::create('cms_theme_components', function (Blueprint $table): void {
            $table->id();
            $table->string('theme_key');
            $table->string('region');
            $table->string('component_key');
            $table->json('view_contract')->nullable();
            $table->json('configuration')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['theme_key', 'region', 'component_key', 'team_id'], 'cms_theme_component_identity');
            $table->index(['theme_key', 'region']);
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_theme_components');
        Schema::dropIfExists('cms_theme_bindings');
    }
};
