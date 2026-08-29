<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_experiments', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->string('type')->default('ab');
            $table->string('status')->default('draft');
            $table->unsignedTinyInteger('allocation_percentage')->default(100);
            $table->json('goals')->nullable();
            $table->json('guardrails')->nullable();
            $table->json('analysis_policy')->nullable();
            $table->string('winner_variant_key')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['key', 'team_id']);
            $table->index(['status', 'team_id']);
        });
        Schema::create('cms_experiment_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experiment_id')->constrained('cms_experiments')->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->json('content')->nullable();
            $table->unsignedTinyInteger('weight');
            $table->timestamps();
            $table->unique(['experiment_id', 'key']);
        });
        Schema::create('cms_experiment_observations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experiment_id')->constrained('cms_experiments')->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('cms_experiment_variants')->cascadeOnDelete();
            $table->string('subject_key');
            $table->string('goal_key')->nullable();
            $table->decimal('value', 14, 4)->default(1);
            $table->timestamp('observed_at');
            $table->timestamps();
            $table->unique(['experiment_id', 'variant_id', 'subject_key', 'goal_key'], 'cms_experiment_observation_identity');
            $table->index(['experiment_id', 'goal_key']);
        });
        Schema::create('cms_experiment_promotions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experiment_id')->constrained('cms_experiments')->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('cms_experiment_variants')->cascadeOnDelete();
            $table->string('actor_type')->nullable();
            $table->string('actor_id')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('promoted_at');
            $table->timestamps();
            $table->index(['experiment_id', 'promoted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_experiment_promotions');
        Schema::dropIfExists('cms_experiment_observations');
        Schema::dropIfExists('cms_experiment_variants');
        Schema::dropIfExists('cms_experiments');
    }
};
