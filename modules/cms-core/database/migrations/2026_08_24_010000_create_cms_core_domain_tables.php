<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_sites', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('domain')->nullable()->unique();
            $table->string('default_locale', 16)->default('en');
            $table->string('timezone', 64)->default('UTC');
            $table->string('status')->default('active')->index();
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('cms_channels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('cms_sites')->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->string('type')->default('web');
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['site_id', 'key']);
        });

        Schema::create('cms_content_identities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('cms_sites')->cascadeOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained('cms_channels')->nullOnDelete();
            $table->string('content_type');
            $table->string('content_id');
            $table->string('canonical_path')->nullable();
            $table->string('status')->default('active')->index();
            $table->nullableMorphs('owner');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['site_id', 'channel_id', 'content_type', 'content_id'], 'cms_content_identity_unique');
            $table->index(['site_id', 'canonical_path']);
        });

        Schema::create('cms_content_aliases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('cms_sites')->cascadeOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained('cms_channels')->nullOnDelete();
            $table->string('alias');
            $table->string('target_type');
            $table->string('target_id');
            $table->unsignedSmallInteger('redirect_status')->default(301);
            $table->timestamps();
            $table->unique(['site_id', 'channel_id', 'alias'], 'cms_content_alias_unique');
        });

        Schema::create('cms_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('cms_sites')->cascadeOnDelete();
            $table->string('key');
            $table->json('value')->nullable();
            $table->string('environment', 32)->default('production');
            $table->timestamps();
            $table->unique(['site_id', 'key', 'environment'], 'cms_setting_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_settings');
        Schema::dropIfExists('cms_content_aliases');
        Schema::dropIfExists('cms_content_identities');
        Schema::dropIfExists('cms_channels');
        Schema::dropIfExists('cms_sites');
    }
};
