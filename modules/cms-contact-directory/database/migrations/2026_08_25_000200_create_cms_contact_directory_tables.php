<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_contact_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
        Schema::create('cms_contact_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 160);
            $table->string('address')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->timestamps();
        });
        Schema::create('cms_contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->foreignId('category_id')->nullable()->constrained('cms_contact_categories')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('cms_contact_locations')->nullOnDelete();
            $table->string('name', 160);
            $table->string('department', 160)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 80)->nullable();
            $table->json('details')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->index(['team_id', 'is_public']);
        });
        Schema::create('cms_contact_forms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('name', 120);
            $table->json('schema');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_contact_forms');
        Schema::dropIfExists('cms_contacts');
        Schema::dropIfExists('cms_contact_locations');
        Schema::dropIfExists('cms_contact_categories');
    }
};
