<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_comments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('commentable_type', 120);
            $table->string('commentable_id', 120);
            $table->foreignId('parent_id')->nullable()->constrained('cms_comments')->nullOnDelete();
            $table->unsignedBigInteger('author_id')->nullable()->index();
            $table->string('guest_name', 160)->nullable();
            $table->string('guest_email', 255)->nullable();
            $table->text('body');
            $table->string('status', 32)->default('pending')->index();
            $table->timestamp('edited_at')->nullable();
            $table->timestamp('moderated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['commentable_type', 'commentable_id', 'status'], 'cms_comments_target_status_idx');
        });
        Schema::create('cms_comment_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('comment_id')->constrained('cms_comments')->cascadeOnDelete();
            $table->unsignedBigInteger('reporter_id')->nullable()->index();
            $table->string('reason', 120);
            $table->string('status', 32)->default('open')->index();
            $table->timestamps();
            $table->unique(['comment_id', 'reporter_id', 'reason'], 'cms_comment_reports_identity_unique');
        });
        Schema::create('cms_comment_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('comment_id')->constrained('cms_comments')->cascadeOnDelete();
            $table->unsignedBigInteger('subscriber_id')->index();
            $table->timestamps();
            $table->unique(['comment_id', 'subscriber_id'], 'cms_comment_subscriptions_identity_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_comment_subscriptions');
        Schema::dropIfExists('cms_comment_reports');
        Schema::dropIfExists('cms_comments');
    }
};
