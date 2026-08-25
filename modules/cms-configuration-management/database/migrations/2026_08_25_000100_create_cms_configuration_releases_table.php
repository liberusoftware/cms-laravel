<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_configuration_releases', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('version', 80);
            $table->string('environment', 80);
            $table->json('payload');
            $table->json('dependencies')->nullable();
            $table->string('checksum', 64);
            $table->string('status', 24)->default('draft')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamp('promoted_at')->nullable();
            $table->timestamp('rolled_back_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'environment', 'version'], 'cms_configuration_releases_identity_unique');
            $table->index(['team_id', 'environment', 'status'], 'cms_configuration_releases_scope_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_configuration_releases');
    }
};
