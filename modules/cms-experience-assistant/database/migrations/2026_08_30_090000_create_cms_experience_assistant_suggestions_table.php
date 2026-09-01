<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_experience_assistant_suggestions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('surface', 180);
            $table->json('definition');
            $table->json('constraints')->nullable();
            $table->json('diagnostics')->nullable();
            $table->string('status', 24)->default('pending');
            $table->string('reviewer_key', 180)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_experience_assistant_suggestions');
    }
};
