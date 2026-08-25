<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_recommendation_lists', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('key');
            $table->string('kind')->default('latest');
            $table->string('ranker')->default('default');
            $table->json('audience_rules')->nullable();
            $table->json('exclusions')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('cms_recommendation_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('list_id')->constrained('cms_recommendation_lists')->cascadeOnDelete();
            $table->string('item_type');
            $table->string('item_key');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->json('context')->nullable();
            $table->decimal('popularity_score', 12, 4)->default(0);
            $table->decimal('editorial_score', 12, 4)->default(0);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['list_id', 'item_type', 'item_key']);
            $table->index(['list_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_recommendation_items');
        Schema::dropIfExists('cms_recommendation_lists');
    }
};
