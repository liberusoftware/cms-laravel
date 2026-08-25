<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_calendar_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 160);
            $table->string('slug', 180);
            $table->text('description')->nullable();
            $table->string('status', 30)->default('planned');
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
        Schema::create('cms_calendar_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->foreignId('campaign_id')->nullable()->constrained('cms_calendar_campaigns')->nullOnDelete();
            $table->string('title', 200);
            $table->string('content_type', 100)->default('content');
            $table->string('subject_key', 180)->nullable();
            $table->string('channel', 100)->nullable();
            $table->string('site', 120)->nullable();
            $table->string('status', 30)->default('planned');
            $table->timestamp('starts_at');
            $table->timestamp('deadline_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->index();
            $table->timestamps();
            $table->index(['team_id', 'starts_at']);
            $table->index(['team_id', 'channel', 'site']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_calendar_items');
        Schema::dropIfExists('cms_calendar_campaigns');
    }
};
