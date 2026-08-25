<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_content_intelligence_insights', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->index();
            $table->string('subject_type', 120);
            $table->string('subject_key', 180);
            $table->string('metric', 60);
            $table->decimal('score', 8, 3)->nullable();
            $table->string('severity', 20)->default('info');
            $table->text('summary');
            $table->text('rationale')->nullable();
            $table->json('context')->nullable();
            $table->string('status', 30)->default('open');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'metric', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_intelligence_insights');
    }
};
