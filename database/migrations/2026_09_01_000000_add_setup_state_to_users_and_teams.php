<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('setup_completed_at')->nullable()->after('current_team_id');
        });

        Schema::table('teams', function (Blueprint $table): void {
            $table->json('settings')->nullable()->after('personal_team');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropColumn('settings');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('setup_completed_at');
        });
    }
};
