<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_forum_references', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('provider', 80);
            $table->string('external_type', 80);
            $table->string('external_id', 180);
            $table->string('url', 2000)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'provider', 'external_type', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_forum_references');
    }
};
