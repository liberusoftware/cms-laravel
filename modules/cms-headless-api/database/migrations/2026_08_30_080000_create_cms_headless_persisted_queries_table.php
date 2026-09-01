<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_headless_persisted_queries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->char('query_hash', 64);
            $table->text('query_body');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'query_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_headless_persisted_queries');
    }
};
