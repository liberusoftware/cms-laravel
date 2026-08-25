<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_content_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 160);
            $table->string('slug', 180);
            $table->string('content_type', 120);
            $table->unsignedInteger('version')->default(1);
            $table->json('schema');
            $table->json('defaults')->nullable();
            $table->boolean('locked')->default(false);
            $table->boolean('published')->default(false);
            $table->unsignedTinyInteger('rollout_percent')->default(100);
            $table->timestamps();
            $table->unique(['team_id', 'slug', 'version']);
            $table->index(['team_id', 'content_type', 'published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_templates');
    }
};
