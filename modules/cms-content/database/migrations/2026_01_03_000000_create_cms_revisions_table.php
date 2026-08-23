<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_revisions', function (Blueprint $table): void {
            $table->id();
            $table->morphs('revisionable');
            $table->unsignedInteger('revision_number');
            $table->json('snapshot')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['revisionable_type', 'revisionable_id', 'revision_number'], 'cms_revisions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_revisions');
    }
};
