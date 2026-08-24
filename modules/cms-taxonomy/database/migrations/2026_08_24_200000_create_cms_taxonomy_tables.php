<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_taxonomies', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('hierarchical')->default(true);
            $table->boolean('exclusive')->default(false);
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
            $table->index('team_id');
        });
        Schema::create('cms_taxonomy_terms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained('cms_taxonomies')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('cms_taxonomy_terms')->nullOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('synonyms')->nullable();
            $table->json('translations')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['taxonomy_id', 'slug']);
            $table->index(['taxonomy_id', 'parent_id']);
            $table->index('team_id');
        });
        Schema::create('cms_taxonomy_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('term_id')->constrained('cms_taxonomy_terms')->cascadeOnDelete();
            $table->string('subject_type');
            $table->string('subject_id');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unique(['term_id', 'subject_type', 'subject_id']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_taxonomy_assignments');
        Schema::dropIfExists('cms_taxonomy_terms');
        Schema::dropIfExists('cms_taxonomies');
    }
};
