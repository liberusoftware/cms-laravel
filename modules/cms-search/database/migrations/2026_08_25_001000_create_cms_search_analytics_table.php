<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_search_analytics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('query', 255);
            $table->unsignedInteger('result_count')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->string('source', 80)->default('api');
            $table->timestamps();
            $table->index(['team_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_search_analytics');
    }
};
