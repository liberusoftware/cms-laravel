<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_content_types', function (Blueprint $table): void {
            $table->unsignedInteger('schema_version')->default(1);
            $table->json('schema_history')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cms_content_types', function (Blueprint $table): void {
            $table->dropColumn(['schema_version', 'schema_history']);
        });
    }
};
