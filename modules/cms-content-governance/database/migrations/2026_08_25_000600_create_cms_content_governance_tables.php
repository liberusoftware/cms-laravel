<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_content_governance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->foreignId('owner_id')->nullable()->index();
            $table->foreignId('steward_id')->nullable()->index();
            $table->json('policy_labels')->nullable();
            $table->string('classification', 40)->default('internal');
            $table->timestamp('review_due_at')->nullable();
            $table->timestamp('retention_until')->nullable();
            $table->boolean('legal_hold')->default(false);
            $table->timestamp('legal_hold_at')->nullable();
            $table->text('legal_hold_reason')->nullable();
            $table->json('evidence')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'subject_type', 'subject_key'], 'cms_governance_subject_unique');
            $table->index(['team_id', 'review_due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_governance_records');
    }
};
