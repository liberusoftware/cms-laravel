<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_migration_jobs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('source');
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('processed_records')->default(0);
            $table->unsignedInteger('failed_records')->default(0);
            $table->json('options')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('cms_migration_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_id')->constrained('cms_migration_jobs')->cascadeOnDelete();
            $table->string('record_type');
            $table->string('source_id');
            $table->string('status')->default('pending')->index();
            $table->json('payload')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['job_id', 'record_type', 'source_id'], 'cms_migration_record_identity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_migration_records');
        Schema::dropIfExists('cms_migration_jobs');
    }
};
