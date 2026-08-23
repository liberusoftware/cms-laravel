<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Throwable;

/**
 * Persists which theme is active in the cms_theme_state table (a single row).
 * Degrades gracefully when the table is absent (e.g. before migration).
 */
final class ThemeStateRepository
{
    private const string TABLE = 'cms_theme_state';

    private ?bool $tableExists = null;

    public function __construct(private readonly ConnectionResolverInterface $db) {}

    public function activeKey(): ?string
    {
        if (! $this->tableExists()) {
            return null;
        }

        try {
            $value = $this->db->connection()->table(self::TABLE)->where('id', 1)->value('active_theme');
        } catch (Throwable) {
            return null;
        }

        return is_string($value) ? $value : null;
    }

    public function setActiveKey(string $key): void
    {
        if ($this->tableExists()) {
            $this->db->connection()->table(self::TABLE)->updateOrInsert(['id' => 1], ['active_theme' => $key]);
        }
    }

    private function tableExists(): bool
    {
        if ($this->tableExists === true) {
            return true;
        }

        try {
            $connection = $this->db->connection();

            if (! $connection instanceof Connection) {
                return false;
            }

            $exists = $connection->getSchemaBuilder()->hasTable(self::TABLE);
        } catch (Throwable) {
            return false;
        }

        if (! $exists) {
            return false;
        }

        return $this->tableExists = true;
    }
}
