<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_block_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('subject_type', 120);
            $table->string('subject_id', 120);
            $table->json('blocks');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('locked')->default(false);
            $table->longText('preview_html')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'subject_type', 'subject_id']);
        });
        Schema::create('cms_block_patterns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 180);
            $table->json('blocks');
            $table->boolean('reusable')->default(true);
            $table->boolean('locked')->default(false);
            $table->timestamps();
            $table->index(['team_id', 'reusable']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_block_patterns');
        Schema::dropIfExists('cms_block_documents');
    }
};
