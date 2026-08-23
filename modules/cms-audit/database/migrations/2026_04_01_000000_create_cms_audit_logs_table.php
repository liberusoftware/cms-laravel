<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('action')->index();
            $table->string('actor_id')->nullable()->index();
            $table->string('actor_label')->nullable();
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            // Plain column, not a HasTenant relation: audit history is never
            // rewritten by tenant scoping.
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable();
            // Append-only: created_at only, no updated_at.
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_audit_logs');
    }
};
