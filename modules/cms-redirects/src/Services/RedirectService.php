<?php

declare(strict_types=1);

namespace Liberu\Cms\Redirects\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Redirects\Models\Redirect;

final class RedirectService
{
    public function create(string $from, string $to, int $statusCode = 301, ?string $source = null, ?int $teamId = null): Redirect
    {
        $from = $this->normalize($from);
        $to = $this->normalize($to);
        if ($from === '/' && $to === '/') {
            throw ValidationException::withMessages(['from_path' => 'Redirect paths are required.']);
        }
        if ($from === $to) {
            throw ValidationException::withMessages(['from_path' => 'A redirect cannot point to itself.']);
        }
        if (! in_array($statusCode, [301, 302, 307, 308], true)) {
            throw ValidationException::withMessages(['status_code' => 'Unsupported redirect status.']);
        }

        return Redirect::updateOrCreate(['from_path' => $from, 'team_id' => $teamId], ['to_path' => $to, 'status_code' => $statusCode, 'source' => $source ?? 'manual', 'active' => true]);
    }

    /** @return array{redirect: Redirect|null, path: string, loop: bool} */
    public function resolve(string $path, int $maxHops = 10): array
    {
        if ($maxHops < 1) {
            throw ValidationException::withMessages(['max_hops' => 'At least one redirect hop is required.']);
        }

        $current = $this->normalize($path);
        $visited = [];
        for ($hop = 0; $hop < $maxHops; $hop++) {
            if (isset($visited[$current])) {
                return ['redirect' => null, 'path' => $current, 'loop' => true];
            }
            $visited[$current] = true;
            $redirect = Redirect::query()->where('from_path', $current)->where('active', true)->first();
            if (! $redirect instanceof Redirect || ! $redirect->isValid()) {
                return ['redirect' => null, 'path' => $current, 'loop' => false];
            }
            $redirect->increment('hit_count');
            $current = $this->normalize($redirect->to_path);
            if (! Redirect::query()->where('from_path', $current)->exists()) {
                return ['redirect' => $redirect->fresh(), 'path' => $current, 'loop' => false];
            }
        }

        return ['redirect' => null, 'path' => $current, 'loop' => true];
    }

    /** @param iterable<array{from_path:string,to_path:string,status_code?:int}> $rows */
    public function import(iterable $rows, ?int $teamId = null): int
    {
        $count = 0;
        foreach ($rows as $row) {
            $this->create($row['from_path'], $row['to_path'], (int) ($row['status_code'] ?? 301), 'import', $teamId);
            $count++;
        }

        return $count;
    }

    public function recordSlugChange(string $oldPath, string $newPath, ?int $teamId = null): Redirect
    {
        return $this->create($oldPath, $newPath, 301, 'slug-change', $teamId);
    }

    /** @return array<int, Redirect> */
    public function suggestions(string $missingPath, int $limit = 5): array
    {
        $needle = trim($this->normalize($missingPath), '/');

        return Redirect::query()->where('active', true)->get()->sortBy(fn (Redirect $redirect): int => levenshtein($needle, trim($redirect->from_path, '/')))->take(max(1, min(20, $limit)))->values()->all();
    }

    private function normalize(string $path): string
    {
        return '/'.ltrim(Str::of($path)->before('?')->trim()->value(), '/');
    }
}
