<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_polls', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('key');
            $table->text('description')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('allow_anonymous')->default(true);
            $table->boolean('allow_multiple')->default(false);
            $table->boolean('active')->default(false)->index();
            $table->boolean('results_public')->default(false);
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('cms_poll_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('poll_id')->constrained('cms_polls')->cascadeOnDelete();
            $table->string('key');
            $table->string('type')->default('single');
            $table->text('prompt');
            $table->json('options')->nullable();
            $table->json('branching')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('required')->default(false);
            $table->timestamps();
            $table->unique(['poll_id', 'key']);
            $table->index(['poll_id', 'position']);
        });
        Schema::create('cms_poll_responses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('poll_id')->constrained('cms_polls')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('respondent_hash')->nullable();
            $table->json('answers');
            $table->timestamp('submitted_at');
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['poll_id', 'respondent_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_poll_responses');
        Schema::dropIfExists('cms_poll_questions');
        Schema::dropIfExists('cms_polls');
    }
};
