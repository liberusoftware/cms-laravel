<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_translation_drafts', function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type');
            $table->string('subject_id');
            $table->string('source_locale');
            $table->string('target_locale');
            $table->longText('source_text');
            $table->longText('translated_text');
            $table->decimal('confidence', 5, 4);
            $table->string('status')->default('draft');
            $table->string('provider');
            $table->string('model');
            $table->json('provenance')->nullable();
            $table->json('violations')->nullable();
            $table->string('reviewer_type')->nullable();
            $table->string('reviewer_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['target_locale', 'status']);
            $table->index('team_id');
        });
        Schema::create('cms_translation_glossary', function (Blueprint $table): void {
            $table->id();
            $table->string('source_locale');
            $table->string('target_locale');
            $table->string('source_term');
            $table->string('preferred_term');
            $table->json('forbidden_terms')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['source_locale', 'target_locale', 'source_term', 'team_id'], 'cms_translation_assistant_glossary_identity');
            $table->index('team_id');
        });
        Schema::create('cms_translation_style_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('locale');
            $table->string('name');
            $table->text('pattern');
            $table->text('message');
            $table->string('severity')->default('warning');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->index(['locale', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_translation_style_rules');
        Schema::dropIfExists('cms_translation_glossary');
        Schema::dropIfExists('cms_translation_drafts');
    }
};
