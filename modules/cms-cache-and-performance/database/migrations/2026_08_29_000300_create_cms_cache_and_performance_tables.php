<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_cache_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('cache_key', 255);
            $table->string('cache_type', 30);
            $table->json('tags')->nullable();
            $table->string('status', 30)->default('cold');
            $table->unsignedInteger('ttl_seconds');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedBigInteger('hits')->default(0);
            $table->unsignedBigInteger('misses')->default(0);
            $table->timestamp('warmed_at')->nullable();
            $table->timestamp('last_invalidated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'cache_key']);
            $table->index(['team_id', 'status', 'cache_type']);
        });
        Schema::create('cms_cache_invalidations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('idempotency_key', 255);
            $table->json('tags')->nullable();
            $table->json('cache_keys')->nullable();
            $table->string('status', 30);
            $table->unsignedInteger('invalidated_count')->default(0);
            $table->text('failure_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_cache_invalidations');
        Schema::dropIfExists('cms_cache_entries');
    }
};
