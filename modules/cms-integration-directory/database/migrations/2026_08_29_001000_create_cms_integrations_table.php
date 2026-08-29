<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_integrations', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 80);
            $table->string('name');
            $table->string('provider', 120);
            $table->string('category', 80)->default('general');
            $table->json('configuration')->nullable();
            $table->string('status', 20)->default('disabled')->index();
            $table->string('health_status', 20)->default('unknown');
            $table->text('health_message')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'key'], 'cms_integration_tenant_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_integrations');
    }
};
