<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_related_content', function (Blueprint $table): void {
            $table->id();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->string('target_type');
            $table->unsignedBigInteger('target_id');
            $table->string('mode')->default('manual');
            $table->decimal('score', 8, 4)->default(0);
            $table->json('explanation')->nullable();
            $table->json('taxonomy')->nullable();
            $table->boolean('excluded')->default(false);
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->unique(
                ['team_id', 'source_type', 'source_id', 'target_type', 'target_id'],
                'cms_related_content_identity_unique'
            );
            $table->index(['source_type', 'source_id', 'excluded', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_related_content');
    }
};
