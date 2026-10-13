<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_membership_contents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('public_id', 36)->unique();
            $table->string('title', 200);
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->string('status', 30)->default('draft');
            $table->text('description')->nullable();
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'subject_type', 'subject_key'], 'cms_membership_content_subject_unique');
        });
        Schema::create('cms_membership_access_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membership_content_id')->constrained('cms_membership_contents')->cascadeOnDelete();
            $table->string('entitlement_key', 160);
            $table->unsignedInteger('minimum_days')->nullable();
            $table->timestamps();
            $table->unique(['membership_content_id', 'entitlement_key'], 'cms_membership_access_rule_unique');
        });
        Schema::create('cms_membership_entitlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->string('entitlement_key', 160);
            $table->string('external_id', 180)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'subject_type', 'subject_key', 'entitlement_key'], 'cms_membership_entitlement_unique');
        });
        Schema::create('cms_membership_drip_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membership_content_id')->constrained('cms_membership_contents')->cascadeOnDelete();
            $table->string('entitlement_key', 160);
            $table->unsignedInteger('delay_days')->default(0);
            $table->timestamps();
            $table->unique(['membership_content_id', 'entitlement_key'], 'cms_membership_drip_unique');
        });
        Schema::create('cms_membership_downloads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membership_content_id')->constrained('cms_membership_contents')->cascadeOnDelete();
            $table->string('public_id', 36)->unique();
            $table->string('path', 500);
            $table->string('filename', 255);
            $table->string('mime_type', 160)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('checksum', 128)->nullable();
            $table->timestamps();
        });
        Schema::create('cms_membership_portal_integrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('provider', 100);
            $table->string('external_id', 180);
            $table->string('status', 30)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'provider', 'external_id'], 'cms_membership_portal_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_membership_portal_integrations');
        Schema::dropIfExists('cms_membership_downloads');
        Schema::dropIfExists('cms_membership_drip_schedules');
        Schema::dropIfExists('cms_membership_entitlements');
        Schema::dropIfExists('cms_membership_access_rules');
        Schema::dropIfExists('cms_membership_contents');
    }
};
