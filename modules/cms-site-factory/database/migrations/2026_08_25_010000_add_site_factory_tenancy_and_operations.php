<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_site_templates', function (Blueprint $table): void {
            $table->foreignId('team_id')->nullable()->after('active')->constrained('teams')->nullOnDelete();
            $table->index(['team_id', 'active']);
        });
        Schema::table('cms_site_domains', function (Blueprint $table): void {
            $table->foreignId('team_id')->nullable()->after('verified_at')->constrained('teams')->nullOnDelete();
            $table->index(['team_id', 'verified_at']);
        });
        Schema::create('cms_site_factory_operations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('cms_sites')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('operation');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['operation', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_site_factory_operations');
        Schema::table('cms_site_domains', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('team_id');
        });
        Schema::table('cms_site_templates', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
