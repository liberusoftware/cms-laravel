<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_translation_vendors', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->string('driver');
            $table->json('settings')->nullable();
            $table->string('status')->default('active')->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });

        Schema::create('cms_translation_jobs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('external_key')->nullable();
            $table->string('name');
            $table->string('source_locale', 16);
            $table->string('target_locale', 16);
            $table->string('status')->default('draft')->index();
            $table->string('vendor_key')->nullable();
            $table->unsignedInteger('total_units')->default(0);
            $table->unsignedInteger('completed_units')->default(0);
            $table->decimal('estimated_cost', 12, 4)->nullable();
            $table->decimal('actual_cost', 12, 4)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('metadata')->nullable();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'external_key']);
            $table->index(['source_locale', 'target_locale', 'status']);
        });

        Schema::create('cms_translation_source_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_id')->constrained('cms_translation_jobs')->cascadeOnDelete();
            $table->string('subject_type');
            $table->string('subject_id');
            $table->string('field');
            $table->longText('source_text');
            $table->char('source_hash', 64);
            $table->string('source_version')->nullable();
            $table->longText('translated_text')->nullable();
            $table->char('translated_hash', 64)->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->decimal('cost', 12, 4)->nullable();
            $table->json('provenance')->nullable();
            $table->json('review_notes')->nullable();
            $table->timestamp('translated_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['job_id', 'subject_type', 'subject_id', 'field', 'source_hash'], 'cms_translation_source_identity');
        });

        Schema::create('cms_translation_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_id')->constrained('cms_translation_jobs')->cascadeOnDelete();
            $table->foreignId('source_change_id')->constrained('cms_translation_source_changes')->cascadeOnDelete();
            $table->string('assignee_type');
            $table->string('assignee_id');
            $table->string('role');
            $table->string('status')->default('assigned')->index();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['source_change_id', 'assignee_type', 'assignee_id', 'role'], 'cms_translation_assignment_identity');
        });

        Schema::create('cms_translation_memory', function (Blueprint $table): void {
            $table->id();
            $table->string('source_locale', 16);
            $table->string('target_locale', 16);
            $table->char('source_hash', 64);
            $table->longText('source_text');
            $table->longText('translated_text');
            $table->string('status')->default('approved')->index();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'source_locale', 'target_locale', 'source_hash'], 'cms_translation_memory_identity');
        });

        Schema::create('cms_translation_glossaries', function (Blueprint $table): void {
            $table->id();
            $table->string('source_locale', 16);
            $table->string('target_locale', 16);
            $table->string('source_term');
            $table->string('preferred_term');
            $table->json('forbidden_terms')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'source_locale', 'target_locale', 'source_term'], 'cms_translation_glossary_identity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_translation_glossaries');
        Schema::dropIfExists('cms_translation_memory');
        Schema::dropIfExists('cms_translation_assignments');
        Schema::dropIfExists('cms_translation_source_changes');
        Schema::dropIfExists('cms_translation_jobs');
        Schema::dropIfExists('cms_translation_vendors');
    }
};
