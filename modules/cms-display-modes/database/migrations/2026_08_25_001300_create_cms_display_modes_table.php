<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_display_modes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->string('content_type', 120);
            $table->string('mode_type', 30)->default('view');
            $table->json('formatters')->nullable();
            $table->json('configuration')->nullable();
            $table->json('responsive_variants')->nullable();
            $table->json('projection')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'slug', 'content_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_display_modes');
    }
};
