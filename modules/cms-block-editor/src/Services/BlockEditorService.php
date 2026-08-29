<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditor\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\BlockEditor\Models\BlockDocument;
use Liberu\Cms\BlockEditor\Models\BlockPattern;
use Liberu\Cms\Blocks\BlockRenderer;
use Liberu\Cms\Blocks\BlockTypeRegistry;

final readonly class BlockEditorService
{
    public function __construct(private BlockTypeRegistry $registry, private BlockRenderer $renderer) {}

    public function save(?int $teamId, string $subjectType, string $subjectId, array $blocks, ?int $expectedVersion = null): BlockDocument
    {
        $this->validateTree($blocks);

        return DB::transaction(function () use ($teamId, $subjectType, $subjectId, $blocks, $expectedVersion): BlockDocument {
            $document = BlockDocument::query()->where('team_id', $teamId)->where('subject_type', $subjectType)->where('subject_id', $subjectId)->lockForUpdate()->first();
            if ($document?->locked) {
                throw ValidationException::withMessages(['document' => 'The block document is locked.']);
            }
            if ($document && $expectedVersion !== null && $document->version !== $expectedVersion) {
                throw ValidationException::withMessages(['version' => 'The block document changed since it was loaded.']);
            }
            $version = ($document?->version ?? 0) + 1;

            return BlockDocument::query()->updateOrCreate(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_id' => $subjectId], ['blocks' => $blocks, 'version' => $version, 'preview_html' => $this->renderer->renderMany($blocks)]);
        });
    }

    public function createPattern(?int $teamId, string $name, array $blocks, bool $reusable = true): BlockPattern
    {
        $this->validateTree($blocks);
        if (trim($name) === '') {
            throw ValidationException::withMessages(['name' => 'A pattern name is required.']);
        }

        return BlockPattern::query()->create(['team_id' => $teamId, 'name' => trim($name), 'blocks' => $blocks, 'reusable' => $reusable, 'locked' => false]);
    }

    public function lock(BlockDocument $document, bool $locked = true): BlockDocument
    {
        $document->update(['locked' => $locked]);

        return $document->fresh();
    }

    private function validateTree(array $blocks, int $depth = 0): void
    {
        if ($depth > 32) {
            throw ValidationException::withMessages(['blocks' => 'Block nesting exceeds the maximum depth.']);
        }
        foreach ($blocks as $block) {
            if (! is_array($block) || ! is_string($block['type'] ?? null) || ! $this->registry->has($block['type'])) {
                throw ValidationException::withMessages(['blocks' => 'Every block must use a registered block type.']);
            }
            if (isset($block['data']) && ! is_array($block['data'])) {
                throw ValidationException::withMessages(['blocks' => 'Block data must be an object.']);
            }
            if (isset($block['children'])) {
                if (! is_array($block['children'])) {
                    throw ValidationException::withMessages(['blocks' => 'Block children must be an array.']);
                }
                $this->validateTree($block['children'], $depth + 1);
            }
        }
    }
}
