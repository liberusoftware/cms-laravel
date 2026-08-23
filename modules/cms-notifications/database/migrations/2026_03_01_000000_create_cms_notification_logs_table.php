<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('event')->index();
            $table->string('channel');
            $table->string('recipient')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->json('context')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_notification_logs');
    }
};
