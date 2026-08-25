<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_delivery_routes', function (Blueprint $table): void {
            $table->id();
            $table->string('path');
            $table->string('route_type')->default('content');
            $table->string('content_type')->nullable();
            $table->string('content_id')->nullable();
            $table->longText('body')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('redirect_url')->nullable();
            $table->unsignedSmallInteger('redirect_status')->nullable();
            $table->json('metadata')->nullable();
            $table->json('cache_tags')->nullable();
            $table->unsignedInteger('cache_ttl')->nullable();
            $table->boolean('preview_enabled')->default(false);
            $table->string('preview_token_hash')->nullable();
            $table->timestamp('preview_expires_at')->nullable();
            $table->boolean('maintenance')->default(false);
            $table->string('status')->default('draft')->index();
            $table->unsignedSmallInteger('error_status')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'path']);
            $table->index(['status', 'route_type']);
        });

        Schema::create('cms_delivery_invalidations', function (Blueprint $table): void {
            $table->id();
            $table->string('idempotency_key');
            $table->json('cache_tags');
            $table->string('status')->default('pending')->index();
            $table->string('provider')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_delivery_invalidations');
        Schema::dropIfExists('cms_delivery_routes');
    }
};
