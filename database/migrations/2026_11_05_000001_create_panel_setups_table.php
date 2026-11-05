<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_platform_admin')->default(false);
        });
        Schema::create('panel_setups', function (Blueprint $table): void {
            $table->id();
            $table->string('scope')->unique();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('data');
            $table->unsignedBigInteger('site_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panel_setups');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_platform_admin'));
    }
};
