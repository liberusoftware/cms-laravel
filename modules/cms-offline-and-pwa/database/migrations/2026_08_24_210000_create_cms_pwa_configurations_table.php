<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pwa_configurations', function (Blueprint $table): void {
            $table->id();
            $table->string('site_key');
            $table->string('name');
            $table->string('short_name', 12);
            $table->string('start_url')->default('/');
            $table->string('scope')->default('/');
            $table->string('display')->default('standalone');
            $table->string('theme_color')->nullable();
            $table->string('background_color')->nullable();
            $table->string('icon_url')->nullable();
            $table->string('offline_url')->default('/offline');
            $table->json('cache_policy')->nullable();
            $table->string('service_worker_version')->default('1');
            $table->timestamp('last_updated_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'site_key']);
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pwa_configurations');
    }
};
