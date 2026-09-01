<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_editorial_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('slug', 200);
            $table->string('title', 240);
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 40)->default('draft');
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('series_id')->nullable();
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();
            $table->json('related_public_ids')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
        Schema::create('cms_editorial_authors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('name', 200);
            $table->string('profile')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });
        Schema::create('cms_editorial_series', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_editorial_posts');
        Schema::dropIfExists('cms_editorial_series');
        Schema::dropIfExists('cms_editorial_authors');
    }
};
