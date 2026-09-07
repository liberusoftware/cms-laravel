<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_media_assistant_suggestions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->uuid('public_id')->unique();
            $table->string('asset_key', 500);
            $table->string('kind', 40);
            $table->text('value');
            $table->string('provider', 120);
            $table->string('model', 120)->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('provenance')->nullable();
            $table->string('status', 30)->default('pending');
            $table->string('reviewer_key', 255)->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'asset_key', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_media_assistant_suggestions');
    }
};
