<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_digital_assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 200);
            $table->string('asset_type', 80);
            $table->string('storage_key', 500);
            $table->string('license', 160)->nullable();
            $table->text('attribution')->nullable();
            $table->text('release_reference')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('renditions')->nullable();
            $table->string('status', 30)->default('draft');
            $table->boolean('brand_asset')->default(false);
            $table->boolean('approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->json('distribution')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'asset_type', 'status']);
            $table->index(['team_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_digital_assets');
    }
};
