<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('event_type', 30);
            $table->string('event_name', 120);
            $table->string('subject_type', 120)->nullable();
            $table->string('subject_id', 120)->nullable();
            $table->string('consent_category', 80)->default('analytics');
            $table->boolean('consent_granted')->default(false);
            $table->string('status', 30)->default('suppressed');
            $table->string('idempotency_key', 255);
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->unique(['team_id', 'idempotency_key']);
            $table->index(['team_id', 'event_type', 'occurred_at']);
        });
        Schema::create('cms_analytics_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('event_type', 30);
            $table->string('provider', 100);
            $table->string('measurement_key', 255);
            $table->string('consent_category', 80)->default('analytics');
            $table->json('config')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'event_type', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_analytics_mappings');
        Schema::dropIfExists('cms_analytics_events');
    }
};
