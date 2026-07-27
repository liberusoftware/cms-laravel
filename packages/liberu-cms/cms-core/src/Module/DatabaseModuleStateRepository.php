<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Module;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Liberu\Cms\Contracts\Module\ModuleStateRepositoryInterface;
use Throwable;

/**
 * Persists enable/disable decisions in the cms_modules table.
 *
 * Reads are memoised for the life of the instance (bound as a singleton) so a
 * request pays at most one query. When the table is absent — a fresh install
 * before migrations — every read falls back to the caller's default so the
 * application still boots.
 */
final class DatabaseModuleStateRepository implements ModuleStateRepositoryInterface
{
    private const string TABLE = 'cms_modules';

    /**
     * Memoised enabled flags, keyed by module key. Absent key = not yet loaded.
     *
     * @var array<string, bool>
     */
    private array $cache = [];

    private ?bool $tableExists = null;

    public function __construct(private readonly ConnectionResolverInterface $db) {}

    public function isEnabled(string $key, bool $default = true): bool
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        if (! $this->tableExists()) {
            return $default;
        }

        try {
            $value = $this->db->connection()
                ->table(self::TABLE)
                ->where('key', $key)
                ->value('enabled');
        } catch (Throwable) {
            return $default;
        }

        if ($value === null) {
            return $default;
        }

        return $this->cache[$key] = $this->normalizeBool($value);
    }

    /**
     * Normalise a boolean read straight off the driver. PostgreSQL's pdo_pgsql
     * can hand back `'t'`/`'f'` strings, and `(bool) 'f'` is `true` — so cast
     * against the known falsey representations instead of leaning on `(bool)`.
     */
    private function normalizeBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return ! in_array($value, [0, '0', 'f', 'false', 'off', 'no', ''], true);
    }

    public function setEnabled(string $key, bool $enabled): void
    {
        if (! $this->tableExists()) {
            return;
        }

        $this->db->connection()->table(self::TABLE)->updateOrInsert(
            ['key' => $key],
            ['enabled' => $enabled, 'updated_at' => now()],
        );

        $this->cache[$key] = $enabled;
    }

    public function forget(string $key): void
    {
        if ($this->tableExists()) {
            $this->db->connection()->table(self::TABLE)->where('key', $key)->delete();
        }

        unset($this->cache[$key]);
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
