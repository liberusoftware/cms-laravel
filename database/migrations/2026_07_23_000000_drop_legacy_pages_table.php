<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Retires the legacy host `pages` table. Pages are now owned by the cms-pages
 * module (`cms_pages`). For an existing deployment with data, copy rows from
 * `pages` into `cms_pages` before this runs — see docs/OPEN-QUESTIONS.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pages');
    }

    public function down(): void
    {
        // Recreating the legacy table is intentionally unsupported; the module
        // owns pages now.
    }
};
