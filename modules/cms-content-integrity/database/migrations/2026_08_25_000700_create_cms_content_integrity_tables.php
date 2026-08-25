<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_integrity_scans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('scope', 120)->default('all');
            $table->string('status', 30)->default('queued');
            $table->unsignedInteger('finding_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('cms_integrity_findings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('scan_id')->constrained('cms_integrity_scans')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->string('kind', 50);
            $table->string('severity', 20)->default('warning');
            $table->text('message');
            $table->json('context')->nullable();
            $table->string('status', 30)->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_integrity_findings');
        Schema::dropIfExists('cms_integrity_scans');
    }
};
