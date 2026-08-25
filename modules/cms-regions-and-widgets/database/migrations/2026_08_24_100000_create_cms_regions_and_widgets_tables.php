<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_regions', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('label');
            $table->string('theme')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('cms_widgets', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->string('type');
            $table->string('title')->nullable();
            $table->json('configuration')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('cms_widget_placements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('region_id')->constrained('cms_regions')->cascadeOnDelete();
            $table->foreignId('widget_id')->constrained('cms_widgets')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->json('visibility')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['region_id', 'widget_id']);
            $table->index(['region_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_widget_placements');
        Schema::dropIfExists('cms_widgets');
        Schema::dropIfExists('cms_regions');
    }
};
