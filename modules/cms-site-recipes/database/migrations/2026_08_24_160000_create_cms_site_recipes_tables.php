<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_site_recipes', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
        });
        Schema::create('cms_site_recipe_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recipe_id')->constrained('cms_site_recipes')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('modules')->nullable();
            $table->json('configuration')->nullable();
            $table->json('content_types')->nullable();
            $table->json('workflows')->nullable();
            $table->json('menus')->nullable();
            $table->json('blocks')->nullable();
            $table->json('themes')->nullable();
            $table->json('starter_content')->nullable();
            $table->string('checksum', 64);
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();
            $table->unique(['recipe_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_site_recipe_versions');
        Schema::dropIfExists('cms_site_recipes');
    }
};
