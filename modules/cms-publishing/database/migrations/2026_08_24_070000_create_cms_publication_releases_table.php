<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_publication_releases', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('state')->default('scheduled')->index();
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('embargo_until')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('review_at')->nullable();
            $table->string('recurrence')->nullable();
            $table->json('targets')->nullable();
            $table->json('cache_tags')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('cms_publication_release_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('release_id')->constrained('cms_publication_releases')->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['release_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_publication_release_events');
        Schema::dropIfExists('cms_publication_releases');
    }
};
