<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_static_builds', function (Blueprint $table): void {
            $table->id();
            $table->string('site_key')->nullable();
            $table->string('state')->default('queued');
            $table->string('kind')->default('full');
            $table->string('deployment')->default('local');
            $table->json('manifest')->nullable();
            $table->json('diagnostics')->nullable();
            $table->unsignedBigInteger('parent_build_id')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->index(['site_key', 'state']);
        });
        Schema::create('cms_static_invalidations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('build_id')->constrained('cms_static_builds')->cascadeOnDelete();
            $table->text('path');
            $table->string('reason');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_static_invalidations');
        Schema::dropIfExists('cms_static_builds');
    }
};
