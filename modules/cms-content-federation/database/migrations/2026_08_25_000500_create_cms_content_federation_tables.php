<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_federation_sources', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 160);
            $table->string('adapter', 80);
            $table->string('endpoint')->nullable();
            $table->string('status', 30)->default('healthy');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_succeeded_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });
        Schema::create('cms_federation_references', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('source_id')->constrained('cms_federation_sources')->cascadeOnDelete();
            $table->string('external_type', 120);
            $table->string('external_key', 180);
            $table->json('payload');
            $table->string('etag', 255)->nullable();
            $table->timestamp('cached_until')->nullable();
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamps();
            $table->unique(['source_id', 'external_type', 'external_key']);
            $table->index('cached_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_federation_references');
        Schema::dropIfExists('cms_federation_sources');
    }
};
