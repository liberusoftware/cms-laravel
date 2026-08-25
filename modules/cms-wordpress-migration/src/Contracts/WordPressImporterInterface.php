<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration\Contracts;

use Liberu\Cms\WordPressMigration\Models\WordPressMigration;

interface WordPressImporterInterface
{
    /** @return iterable<array{record_type:string,source_id:string,payload:array<string,mixed>,source_identifiers?:array<string,mixed>}> */
    public function records(WordPressMigration $migration): iterable;
}
