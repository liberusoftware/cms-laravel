<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_sitemap_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('type')->default('web');
            $table->string('locale', 16)->nullable();
            $table->text('url');
            $table->timestamp('last_modified')->nullable();
            $table->decimal('priority', 3, 2)->default(0.5);
            $table->string('change_frequency')->nullable();
            $table->json('images')->nullable();
            $table->json('video')->nullable();
            $table->json('news')->nullable();
            $table->boolean('excluded')->default(false);
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(['site_id', 'type', 'locale', 'url']);
            $table->index(['site_id', 'type', 'locale', 'excluded']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_sitemap_entries');
    }
};
