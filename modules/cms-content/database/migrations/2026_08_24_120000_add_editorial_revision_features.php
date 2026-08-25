<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_revisions', function (Blueprint $table): void {
            $table->string('branch')->default('main')->after('user_id');
            $table->boolean('autosave')->default(false)->after('branch');
            $table->unsignedBigInteger('parent_revision_id')->nullable()->after('autosave');
            $table->boolean('published')->default(false)->after('parent_revision_id');
            $table->json('metadata')->nullable()->after('published');
            $table->string('content_hash', 64)->nullable()->after('metadata');
            $table->index(
                ['revisionable_type', 'revisionable_id', 'branch', 'autosave'],
                'cms_revisions_branch_autosave_idx'
            );
        });
        Schema::table('cms_revisions', function (Blueprint $table): void {
            $table->dropUnique('cms_revisions_unique');
            $table->unique(['revisionable_type', 'revisionable_id', 'branch', 'revision_number'], 'cms_revisions_branch_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cms_revisions', function (Blueprint $table): void {
            $table->dropUnique('cms_revisions_branch_unique');
            $table->unique(['revisionable_type', 'revisionable_id', 'revision_number'], 'cms_revisions_unique');
            $table->dropIndex(['cms_revisions_revisionable_type_revisionable_id_branch_autosave_index']);
            $table->dropColumn(['branch', 'autosave', 'parent_revision_id', 'published', 'metadata', 'content_hash']);
        });
    }
};
