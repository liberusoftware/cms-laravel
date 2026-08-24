<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_personalization_audiences', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('key');
            $table->json('rules')->default('{}');
            $table->boolean('requires_consent')->default(false);
            $table->boolean('active')->default(true)->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('cms_personalization_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('audience_id')->constrained('cms_personalization_audiences')->cascadeOnDelete();
            $table->string('key');
            $table->json('payload');
            $table->unsignedInteger('priority')->default(0);
            $table->unsignedTinyInteger('holdout_percent')->default(0);
            $table->boolean('fallback')->default(false);
            $table->boolean('active')->default(true)->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['audience_id', 'key']);
        });
        Schema::create('cms_personalization_decisions', function (Blueprint $table): void {
            $table->id();
            $table->string('audience_key');
            $table->string('variant_key')->nullable();
            $table->string('subject_key')->nullable();
            $table->json('context')->default('{}');
            $table->string('reason');
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->index(['audience_key', 'subject_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_personalization_decisions');
        Schema::dropIfExists('cms_personalization_variants');
        Schema::dropIfExists('cms_personalization_audiences');
    }
};
