<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_knowledge_base_articles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('cms_knowledge_base_articles')->nullOnDelete();
            $table->string('slug', 180);
            $table->string('title', 240);
            $table->text('body');
            $table->string('status', 24)->default('draft');
            $table->unsignedInteger('search_weight')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('review_due_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
            $table->index(['team_id', 'status']);
        });
        Schema::create('cms_knowledge_base_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_id')->constrained('cms_knowledge_base_articles')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->text('body');
            $table->string('author_key', 180);
            $table->timestamps();
            $table->unique(['article_id', 'version']);
        });
        Schema::create('cms_knowledge_base_feedback', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_id')->constrained('cms_knowledge_base_articles')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->boolean('helpful');
            $table->string('comment', 1000)->nullable();
            $table->string('reporter_key', 180)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_knowledge_base_feedback');
        Schema::dropIfExists('cms_knowledge_base_versions');
        Schema::dropIfExists('cms_knowledge_base_articles');
    }
};
