<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_menus', function (Blueprint $table): void {
            $table->unsignedBigInteger('team_id')->nullable()->index();
        });

        Schema::table('cms_menu_items', function (Blueprint $table): void {
            $table->unsignedBigInteger('team_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('cms_menu_items', function (Blueprint $table): void {
            $table->dropColumn('team_id');
        });

        Schema::table('cms_menus', function (Blueprint $table): void {
            $table->dropColumn('team_id');
        });
    }
};
