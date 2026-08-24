<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_collections', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('record')->index();
            $table->text('description')->nullable();
            $table->json('schema')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('cms_collection_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('collection_id')->constrained('cms_collections')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->json('data')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['collection_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_collection_items');
        Schema::dropIfExists('cms_collections');
    }
};
