<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_embed_providers', function (Blueprint $t): void {
            $t->id();
            $t->string('key');
            $t->string('name');
            $t->string('domain_pattern')->nullable();
            $t->string('privacy_domain')->nullable();
            $t->string('status')->default('active');
            $t->json('config')->nullable();
            $t->foreignId('team_id')->nullable()->index();
            $t->timestamps();
            $t->unique(['team_id', 'key']);
        });
        Schema::create('cms_embeds', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('provider_id')->constrained('cms_embed_providers')->cascadeOnDelete();
            $t->string('external_key');
            $t->string('url');
            $t->string('title')->nullable();
            $t->string('privacy_mode')->default('public');
            $t->boolean('consent_required')->default(false);
            $t->string('fallback_url')->nullable();
            $t->string('aspect_ratio')->nullable();
            $t->boolean('responsive')->default(true);
            $t->string('status')->default('draft');
            $t->json('metadata')->nullable();
            $t->foreignId('team_id')->nullable()->index();
            $t->timestamps();
            $t->unique(['team_id', 'provider_id', 'external_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_embeds');
        Schema::dropIfExists('cms_embed_providers');
    }
};
