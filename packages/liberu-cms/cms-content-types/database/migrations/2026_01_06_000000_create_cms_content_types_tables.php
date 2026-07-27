<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_content_types', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('singular_label');
            $table->string('plural_label');
            $table->json('fields')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_content_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('content_type_id')->constrained('cms_content_types')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            // longText, not json: search() runs a portable `LIKE` over the raw
            // payload, and PostgreSQL rejects `LIKE` against a json/jsonb column.
            // The model's `array` cast still (de)serializes it transparently.
            $table->longText('data')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_entries');
        Schema::dropIfExists('cms_content_types');
    }
};
