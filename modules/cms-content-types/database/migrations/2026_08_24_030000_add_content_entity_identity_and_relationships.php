<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_content_entries', function (Blueprint $table): void {
            $table->string('canonical_id')->nullable()->unique();
            $table->unsignedBigInteger('author_id')->nullable()->index();
        });

        Schema::create('cms_content_entry_relationships', function (Blueprint $table): void {
            $table->foreignId('source_entry_id')->constrained('cms_content_entries')->cascadeOnDelete();
            $table->foreignId('target_entry_id')->constrained('cms_content_entries')->cascadeOnDelete();
            $table->string('relation');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->primary(['source_entry_id', 'target_entry_id', 'relation']);
            $table->index(['source_entry_id', 'relation', 'position'], 'cms_content_entry_rel_source_relation_position_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_entry_relationships');
        Schema::table('cms_content_entries', function (Blueprint $table): void {
            $table->dropUnique(['canonical_id']);
            $table->dropColumn(['canonical_id', 'author_id']);
        });
    }
};
