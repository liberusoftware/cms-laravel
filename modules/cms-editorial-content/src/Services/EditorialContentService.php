<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContent\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\EditorialContent\Models\EditorialAuthor;
use Liberu\Cms\EditorialContent\Models\EditorialPost;
use Liberu\Cms\EditorialContent\Models\EditorialSeries;

final class EditorialContentService
{
    /** @param array<string, mixed> $data */
    public function post(array $data, ?int $teamId = null): EditorialPost
    {
        $slug = $this->string($data, 'slug');
        $title = $this->string($data, 'title');
        $status = $data['status'] ?? 'draft';
        if (! is_string($status) || ! in_array($status, ['draft', 'published', 'archived'], true)) {
            throw ValidationException::withMessages(['status' => 'The editorial status is invalid.']);
        }

        return EditorialPost::query()->create([...$data, 'team_id' => $teamId, 'public_id' => (string) Str::uuid(), 'slug' => $slug, 'title' => $title, 'status' => $status, 'published_at' => $status === 'published' ? now() : null, 'archived_at' => $status === 'archived' ? now() : null]);
    }

    public function publish(EditorialPost $post): EditorialPost
    {
        $post->update(['status' => 'published', 'published_at' => now(), 'archived_at' => null]);

        return $post->refresh();
    }

    public function archive(EditorialPost $post): EditorialPost
    {
        $post->update(['status' => 'archived', 'archived_at' => now()]);

        return $post->refresh();
    }

    public function author(string $name, ?int $teamId = null, ?string $profile = null): EditorialAuthor
    {
        $this->stringValue($name, 'name');

        return EditorialAuthor::query()->firstOrCreate(['team_id' => $teamId, 'name' => $name], ['public_id' => (string) Str::uuid(), 'profile' => $profile]);
    }

    public function series(string $name, ?int $teamId = null, ?string $description = null): EditorialSeries
    {
        $this->stringValue($name, 'name');

        return EditorialSeries::query()->firstOrCreate(['team_id' => $teamId, 'name' => $name], ['public_id' => (string) Str::uuid(), 'description' => $description]);
    }

    /** @param array<string, mixed> $data */
    private function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;
        $this->stringValue($value, $key);

        if (! is_string($value)) {
            throw ValidationException::withMessages([$key => 'A valid string is required.']);
        }

        return $value;
    }

    private function stringValue(mixed $value, string $field): void
    {
        if (! is_string($value) || trim($value) === '' || strlen($value) > 240) {
            throw ValidationException::withMessages([$field => 'A valid non-empty value is required.']);
        }
    }
}
