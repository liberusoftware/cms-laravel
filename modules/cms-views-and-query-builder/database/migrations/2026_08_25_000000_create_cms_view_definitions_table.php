<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_view_definitions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('source');
            $table->text('description')->nullable();
            $table->json('definition');
            $table->string('visibility')->default('private')->index();
            $table->string('status')->default('draft')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_view_definitions');
    }
};
