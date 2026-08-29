<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_copilot_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('capability', 40);
            $table->text('prompt');
            $table->json('input')->nullable();
            $table->json('result')->nullable();
            $table->string('status', 40)->default('pending');
            $table->string('idempotency_key', 255);
            $table->string('confirmation_hash', 64)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'idempotency_key']);
            $table->index(['team_id', 'capability', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_copilot_requests');
    }
};
