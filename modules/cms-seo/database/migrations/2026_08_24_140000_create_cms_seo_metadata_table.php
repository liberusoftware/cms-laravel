<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_seo_metadata', function (Blueprint $table): void {
            $table->id();
            $table->string('seoable_type');
            $table->unsignedBigInteger('seoable_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('canonical_url')->nullable();
            $table->string('robots')->default('index,follow');
            $table->json('structured_data')->nullable();
            $table->json('social_cards')->nullable();
            $table->json('hreflang')->nullable();
            $table->boolean('noindex')->default(false);
            $table->boolean('noarchive')->default(false);
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'seoable_type', 'seoable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_seo_metadata');
    }
};
