<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_extension_publishers', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->string('website')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['key', 'team_id']);
            $table->index('team_id');
        });
        Schema::create('cms_extension_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('cms_extension_listings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('publisher_id')->constrained('cms_extension_publishers')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('cms_extension_categories')->nullOnDelete();
            $table->string('key');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('license')->default('proprietary');
            $table->string('status')->default('draft');
            $table->string('security_status')->default('pending');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['key', 'team_id']);
            $table->index(['status', 'security_status']);
            $table->index('team_id');
        });
        Schema::create('cms_extension_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained('cms_extension_listings')->cascadeOnDelete();
            $table->string('version');
            $table->string('download_url');
            $table->string('checksum', 128);
            $table->text('signature')->nullable();
            $table->string('signing_key')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['listing_id', 'version']);
            $table->index('status');
        });
        Schema::create('cms_extension_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained('cms_extension_listings')->cascadeOnDelete();
            $table->string('reviewer_type');
            $table->string('reviewer_id');
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->string('status')->default('published');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['listing_id', 'reviewer_type', 'reviewer_id', 'team_id']);
        });
        Schema::create('cms_extension_licenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained('cms_extension_listings')->cascadeOnDelete();
            $table->string('license_key');
            $table->string('subject_type');
            $table->string('subject_id');
            $table->string('status')->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->unique(['listing_id', 'license_key']);
            $table->index(['subject_type', 'subject_id']);
        });
        Schema::create('cms_extension_trials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained('cms_extension_listings')->cascadeOnDelete();
            $table->string('subject_type');
            $table->string('subject_id');
            $table->string('status')->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamps();
            $table->unique(['listing_id', 'subject_type', 'subject_id']);
        });
        Schema::create('cms_extension_support', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained('cms_extension_listings')->cascadeOnDelete();
            $table->string('channel');
            $table->string('url')->nullable();
            $table->unsignedInteger('response_hours')->nullable();
            $table->timestamps();
            $table->unique(['listing_id', 'channel']);
        });
        Schema::create('cms_extension_distributions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('version_id')->constrained('cms_extension_versions')->cascadeOnDelete();
            $table->string('channel')->default('stable');
            $table->string('url');
            $table->string('checksum', 128);
            $table->string('status')->default('available');
            $table->timestamps();
            $table->unique(['version_id', 'channel']);
        });
    }

    public function down(): void
    {
        foreach (['cms_extension_distributions', 'cms_extension_support', 'cms_extension_trials', 'cms_extension_licenses', 'cms_extension_reviews', 'cms_extension_versions', 'cms_extension_listings', 'cms_extension_categories', 'cms_extension_publishers'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
