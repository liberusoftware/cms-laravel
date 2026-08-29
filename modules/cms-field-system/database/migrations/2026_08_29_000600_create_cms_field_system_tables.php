<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_field_schemas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('key', 255);
            $table->string('name', 180);
            $table->unsignedInteger('version')->default(1);
            $table->json('fields');
            $table->json('history')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_field_schemas');
    }
};
