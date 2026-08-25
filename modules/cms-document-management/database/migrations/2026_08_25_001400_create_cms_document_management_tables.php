<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('title');
            $table->string('slug');
            $table->string('path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('status')->default('draft')->index();
            $table->text('extracted_text')->nullable();
            $table->timestamp('retention_until')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
        Schema::create('cms_document_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('cms_documents')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('path')->nullable();
            $table->string('checksum')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['document_id', 'version']);
        });
        Schema::create('cms_document_previews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('cms_documents')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('path')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
        Schema::create('cms_document_access', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('cms_documents')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('permission');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['document_id', 'user_id']);
        });
        Schema::create('cms_document_downloads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('cms_documents')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->timestamp('downloaded_at');
            $table->timestamps();
        });
        Schema::create('cms_document_watermarks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('cms_documents')->cascadeOnDelete();
            $table->string('label');
            $table->json('configuration')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_document_watermarks');
        Schema::dropIfExists('cms_document_downloads');
        Schema::dropIfExists('cms_document_access');
        Schema::dropIfExists('cms_document_previews');
        Schema::dropIfExists('cms_document_versions');
        Schema::dropIfExists('cms_documents');
    }
};
