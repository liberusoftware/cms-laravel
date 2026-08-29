<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_backup_artifacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 180);
            $table->string('artifact_type', 30);
            $table->string('status', 30)->default('available');
            $table->string('disk', 80);
            $table->string('path', 500);
            $table->unsignedBigInteger('size')->default(0);
            $table->string('checksum', 128)->nullable();
            $table->boolean('encrypted')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'artifact_type']);
        });
        Schema::create('cms_backup_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 180);
            $table->string('frequency', 20);
            $table->json('artifact_types');
            $table->unsignedInteger('retention_days')->default(30);
            $table->boolean('enabled')->default(true);
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'enabled', 'next_run_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_backup_schedules');
        Schema::dropIfExists('cms_backup_artifacts');
    }
};
