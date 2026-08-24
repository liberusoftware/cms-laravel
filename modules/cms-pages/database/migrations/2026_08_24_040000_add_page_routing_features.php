<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->boolean('is_home')->default(false)->index();
            $table->boolean('is_error')->default(false)->index();
        });

        Schema::create('cms_page_aliases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->constrained('cms_pages')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'path']);
        });

        Schema::create('cms_page_redirects', function (Blueprint $table): void {
            $table->id();
            $table->string('from_path');
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('active')->default(true)->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['team_id', 'from_path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_page_redirects');
        Schema::dropIfExists('cms_page_aliases');
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropColumn(['is_home', 'is_error']);
        });
    }
};
