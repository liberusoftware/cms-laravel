<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_editorial_workflows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('key', 120);
            $table->string('name', 200);
            $table->string('initial_state', 120)->default('draft');
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('cms_editorial_workflow_states', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('cms_editorial_workflows')->cascadeOnDelete();
            $table->string('key', 120);
            $table->string('label', 200);
            $table->boolean('terminal')->default(false);
            $table->timestamps();
            $table->unique(['workflow_id', 'key']);
        });
        Schema::create('cms_editorial_workflow_transitions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('cms_editorial_workflows')->cascadeOnDelete();
            $table->string('from_state', 120);
            $table->string('to_state', 120);
            $table->string('permission', 160)->nullable();
            $table->boolean('requires_review')->default(false);
            $table->timestamps();
            $table->unique(['workflow_id', 'from_state', 'to_state']);
        });
        Schema::create('cms_editorial_workflow_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('cms_editorial_workflows')->cascadeOnDelete();
            $table->string('subject_type', 120);
            $table->string('subject_key', 255);
            $table->string('actor_type', 120);
            $table->string('actor_key', 255);
            $table->string('role', 80)->default('assignee');
            $table->string('status', 40)->default('active');
            $table->foreignId('delegated_from_id')->nullable()->constrained('cms_editorial_workflow_assignments')->nullOnDelete();
            $table->timestamps();
            $table->index(['workflow_id', 'subject_type', 'subject_key']);
        });
        Schema::create('cms_editorial_workflow_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('cms_editorial_workflows')->cascadeOnDelete();
            $table->string('subject_type', 120);
            $table->string('subject_key', 255);
            $table->string('reviewer_key', 255);
            $table->string('decision', 40);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->index(['workflow_id', 'subject_type', 'subject_key']);
        });
        Schema::create('cms_editorial_workflow_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('cms_editorial_workflows')->cascadeOnDelete();
            $table->string('subject_type', 120);
            $table->string('subject_key', 255);
            $table->string('event', 120);
            $table->string('actor_key', 255)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->index(['workflow_id', 'subject_type', 'subject_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_editorial_workflow_evidence');
        Schema::dropIfExists('cms_editorial_workflow_reviews');
        Schema::dropIfExists('cms_editorial_workflow_assignments');
        Schema::dropIfExists('cms_editorial_workflow_transitions');
        Schema::dropIfExists('cms_editorial_workflow_states');
        Schema::dropIfExists('cms_editorial_workflows');
    }
};
