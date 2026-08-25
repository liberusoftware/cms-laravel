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
            $table->string('variant')->default('default')->after('location');
            $table->json('settings')->nullable()->after('variant');
        });

        Schema::table('cms_menu_items', function (Blueprint $table): void {
            $table->string('link_type')->default('custom')->after('url');
            $table->string('content_id')->nullable()->after('link_type');
            $table->string('system_route')->nullable()->after('content_id');
            $table->json('visibility')->nullable()->after('permission');
            $table->boolean('active')->default(true)->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('cms_menu_items', function (Blueprint $table): void {
            $table->dropColumn(['link_type', 'content_id', 'system_route', 'visibility', 'active']);
        });

        Schema::table('cms_menus', function (Blueprint $table): void {
            $table->dropColumn(['variant', 'settings']);
        });
    }
};
