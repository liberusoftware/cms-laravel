<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->string('subscriber_type', 120);
            $table->string('subscriber_id');
            $table->string('subject_type', 120);
            $table->string('subject_id');
            $table->string('frequency')->default('instant');
            $table->json('channels');
            $table->string('locale')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['subscriber_type', 'subscriber_id', 'subject_type', 'subject_id', 'team_id'], 'cms_subscription_identity');
            $table->index(['subject_type', 'subject_id']);
            $table->index('team_id');
        });
        Schema::create('cms_subscription_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscription_id')->constrained('cms_subscriptions')->cascadeOnDelete();
            $table->string('event');
            $table->string('channel');
            $table->json('payload')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['subscription_id', 'event', 'channel']);
            $table->index(['status', 'channel']);
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_subscription_deliveries');
        Schema::dropIfExists('cms_subscriptions');
    }
};
