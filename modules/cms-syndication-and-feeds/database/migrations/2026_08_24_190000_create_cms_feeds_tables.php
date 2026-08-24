<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_feeds', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('format')->default('rss');
            $table->string('source_url')->nullable();
            $table->json('mapping')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('cms_feed_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('feed_id')->constrained('cms_feeds')->cascadeOnDelete();
            $table->string('external_id');
            $table->string('title');
            $table->text('url');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->json('attribution')->nullable();
            $table->string('dedupe_hash', 64);
            $table->json('payload')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['feed_id', 'dedupe_hash']);
        });
        Schema::create('cms_syndication_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('feed_id')->constrained('cms_feeds')->cascadeOnDelete();
            $table->string('destination');
            $table->string('status')->default('queued');
            $table->json('response')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_syndication_deliveries');
        Schema::dropIfExists('cms_feed_items');
        Schema::dropIfExists('cms_feeds');
    }
};
