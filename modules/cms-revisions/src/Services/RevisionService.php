<?php

declare(strict_types=1);

namespace Liberu\Cms\Revisions\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Content\Revisions\Revision;

final class RevisionService
{
    public function create(string $type, int $id, array $snapshot, ?int $userId = null, string $branch = 'main', bool $autosave = false, array $metadata = []): Revision
    {
        $parent = Revision::query()->where('revisionable_type', $type)->where('revisionable_id', $id)->where('branch', $branch)->latest('revision_number')->first();
        $number = (int) ($parent?->revision_number ?? 0) + 1;

        return Revision::query()->create(['revisionable_type' => $type, 'revisionable_id' => $id, 'revision_number' => $number, 'snapshot' => $snapshot, 'user_id' => $userId, 'branch' => $branch, 'autosave' => $autosave, 'parent_revision_id' => $parent?->getKey(), 'published' => false, 'metadata' => $metadata, 'content_hash' => hash('sha256', (string) json_encode($snapshot, JSON_THROW_ON_ERROR)), 'created_at' => now()]);
    }

    public function autosave(string $type, int $id, array $snapshot, ?int $userId = null, string $branch = 'main'): Revision
    {
        $existing = Revision::query()->where('revisionable_type', $type)->where('revisionable_id', $id)->where('branch', $branch)->where('autosave', true)->latest('created_at')->first();
        if ($existing instanceof Revision && $existing->content_hash === hash('sha256', (string) json_encode($snapshot, JSON_THROW_ON_ERROR))) {
            return $existing;
        }
        if ($existing instanceof Revision) {
            $existing->delete();
        }

        return $this->create($type, $id, $snapshot, $userId, $branch, true, ['kind' => 'autosave']);
    }

    /** @return array{from: array<string,mixed>, to: array<string,mixed>, changes: array<int,array{path:string,from:mixed,to:mixed}>} */
    public function compare(Revision $from, Revision $to): array
    {
        $changes = [];
        $keys = array_unique([...array_keys($from->snapshot()), ...array_keys($to->snapshot())]);
        foreach ($keys as $key) {
            if (($from->snapshot()[$key] ?? null) !== ($to->snapshot()[$key] ?? null)) {
                $changes[] = ['path' => (string) $key, 'from' => $from->snapshot()[$key] ?? null, 'to' => $to->snapshot()[$key] ?? null];
            }
        }

        return ['from' => $from->snapshot(), 'to' => $to->snapshot(), 'changes' => $changes];
    }

    public function restore(Revision $revision, ?int $userId = null, string $branch = 'main'): Revision
    {
        return $this->create($revision->revisionable_type, $revision->revisionable_id, $revision->snapshot(), $userId, $branch, false, ['kind' => 'restore', 'restored_revision_id' => $revision->getKey()]);
    }

    public function branch(Revision $revision, string $branch, ?int $userId = null): Revision
    {
        if ($branch === '' || ! Str::of($branch)->isAscii()) {
            throw ValidationException::withMessages(['branch' => 'A branch name is required.']);
        }

        return $this->create($revision->revisionable_type, $revision->revisionable_id, $revision->snapshot(), $userId, Str::slug($branch), false, ['kind' => 'branch', 'source_revision_id' => $revision->getKey()]);
    }

    public function publish(Revision $revision): Revision
    {
        DB::transaction(function () use ($revision): void {
            Revision::query()->where('revisionable_type', $revision->revisionable_type)->where('revisionable_id', $revision->revisionable_id)->where('branch', $revision->branch)->update(['published' => false]);
            $revision->forceFill(['published' => true, 'autosave' => false])->save();
        });

        return $revision->fresh();
    }

    public function prune(string $type, int $id, int $retain = 20, string $branch = 'main'): int
    {
        $keep = Revision::query()->where('revisionable_type', $type)->where('revisionable_id', $id)->where('branch', $branch)->where('autosave', false)->latest('revision_number')->limit(max(1, $retain))->pluck('id');

        return Revision::query()->where('revisionable_type', $type)->where('revisionable_id', $id)->where('branch', $branch)->whereNotIn('id', $keep)->where('published', false)->delete();
    }
}
