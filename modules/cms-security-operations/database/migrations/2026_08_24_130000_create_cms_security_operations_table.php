<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_security_operations', function (Blueprint $table): void {
            $table->id();
            $table->string('kind');
            $table->string('subject')->nullable();
            $table->string('status');
            $table->json('evidence')->nullable();
            $table->string('content_hash', 64)->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->timestamps();
            $table->index(['kind', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_security_operations');
    }
};
