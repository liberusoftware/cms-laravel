<?php

declare(strict_types=1);

namespace Liberu\Cms\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property string $disk
 * @property string $path
 * @property string $file_name
 * @property string $mime_type
 * @property int $size
 * @property string|null $folder
 * @property array<string, mixed>|null $metadata
 * @property int|null $team_id
 */
final class Media extends Model implements MediaItemInterface
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_media';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'disk',
        'path',
        'file_name',
        'mime_type',
        'size',
        'folder',
        'metadata',
        'team_id',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function mediaId(): int
    {
        return $this->id;
    }

    public function disk(): string
    {
        return $this->disk;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function fileName(): string
    {
        return $this->file_name;
    }

    public function mimeType(): string
    {
        return $this->mime_type;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function folder(): ?string
    {
        return $this->folder;
    }

    public function metadata(): array
    {
        return $this->metadata ?? [];
    }
}
