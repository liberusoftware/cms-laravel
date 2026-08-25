<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_multisite_admins', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('cms_sites')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->string('role')->default('editor');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['site_id', 'user_id']);
        });

        Schema::create('cms_multisite_quotas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->unique()->constrained('cms_sites')->cascadeOnDelete();
            $table->json('limits')->nullable();
            $table->json('usage')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('cms_multisite_references', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('source_site_id')->constrained('cms_sites')->cascadeOnDelete();
            $table->foreignId('target_site_id')->constrained('cms_sites')->cascadeOnDelete();
            $table->string('content_type');
            $table->string('content_id');
            $table->string('mode')->default('shared');
            $table->string('status')->default('active')->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['source_site_id', 'target_site_id', 'content_type', 'content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_multisite_references');
        Schema::dropIfExists('cms_multisite_quotas');
        Schema::dropIfExists('cms_multisite_admins');
    }
};
