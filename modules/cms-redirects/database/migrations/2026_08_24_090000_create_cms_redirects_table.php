<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_redirects', function (Blueprint $table): void {
            $table->id();
            $table->string('from_path');
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedBigInteger('hit_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->string('source')->default('manual');
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'from_path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_redirects');
    }
};
