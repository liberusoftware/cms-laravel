<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_content_locks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->foreignId('holder_id')->nullable()->index();
            $table->string('token', 64)->unique();
            $table->unsignedInteger('version')->default(1);
            $table->json('snapshot')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->unique(['team_id', 'subject_type', 'subject_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_locks');
    }
};
