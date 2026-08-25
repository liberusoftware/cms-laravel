<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_content_access_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->string('visibility', 30)->default('public');
            $table->json('audiences')->nullable();
            $table->json('fields')->nullable();
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->boolean('preview_allowed')->default(false);
            $table->timestamps();
            $table->unique(['team_id', 'subject_type', 'subject_key'], 'cms_access_subject_unique');
        });
        Schema::create('cms_content_private_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('token_hash', 64)->unique();
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->timestamp('expires_at');
            $table->unsignedInteger('uses')->default(0);
            $table->unsignedInteger('max_uses')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_private_links');
        Schema::dropIfExists('cms_content_access_rules');
    }
};
