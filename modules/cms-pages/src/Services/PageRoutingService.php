<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Pages\Models\PageAlias;
use Liberu\Cms\Pages\Models\PageRedirect;

/** Owns canonical paths, aliases, and redirect invariants for Pages. */
final class PageRoutingService
{
    public function addAlias(Page $page, string $path): PageAlias
    {
        $path = $this->path($path, 'path');

        if ($path === $page->path() || $this->canonicalPathExists($page, $path)) {
            throw ValidationException::withMessages(['path' => 'The alias conflicts with a canonical page path.']);
        }

        return $page->aliases()->firstOrCreate(['path' => $path], ['team_id' => $page->team_id]);
    }

    /** @param array{from_path:string,to_path:string,status_code?:int,active?:bool,team_id?:int|null} $attributes */
    public function createRedirect(array $attributes): PageRedirect
    {
        $attributes['from_path'] = $this->path($attributes['from_path'], 'from_path');
        $attributes['to_path'] = $this->path($attributes['to_path'], 'to_path');
        $this->validateRedirect($attributes['from_path'], $attributes['to_path'], $attributes['status_code'] ?? 301);

        return PageRedirect::create($attributes + ['status_code' => 301, 'active' => true]);
    }

    /** @param array<string, mixed> $attributes */
    public function updateRedirect(PageRedirect $redirect, array $attributes): PageRedirect
    {
        $from = $this->path((string) ($attributes['from_path'] ?? $redirect->from_path), 'from_path');
        $to = $this->path((string) ($attributes['to_path'] ?? $redirect->to_path), 'to_path');
        $status = (int) ($attributes['status_code'] ?? $redirect->status_code);
        $this->validateRedirect($from, $to, $status, $redirect->getKey());

        $redirect->update($attributes + ['from_path' => $from, 'to_path' => $to, 'status_code' => $status]);

        return $redirect->refresh();
    }

    public function deleteRedirect(PageRedirect $redirect): void
    {
        $redirect->delete();
    }

    private function canonicalPathExists(Page $page, string $path): bool
    {
        return Page::query()->get()->contains(fn (Page $candidate): bool => $candidate->isNot($page) && $candidate->path() === $path);
    }

    private function path(string $path, string $field): string
    {
        $path = '/'.trim($path, '/');

        if ($path === '/') {
            throw ValidationException::withMessages([$field => 'A root path is not valid here.']);
        }

        return $path;
    }

    private function validateRedirect(string $from, string $to, int $status, ?int $ignoreId = null): void
    {
        if ($from === $to) {
            throw ValidationException::withMessages(['to_path' => 'A redirect cannot point to itself.']);
        }

        if (! in_array($status, [301, 302, 307, 308], true)) {
            throw ValidationException::withMessages(['status_code' => 'The status code must be 301, 302, 307, or 308.']);
        }

        if ($this->pagePathExists($from)) {
            throw ValidationException::withMessages(['from_path' => 'A redirect cannot shadow a page or alias path.']);
        }

        $query = PageRedirect::query()->where('from_path', $from);
        if ($ignoreId !== null) {
            $query->where($query->getModel()->qualifyColumn($query->getModel()->getKeyName()), '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages(['from_path' => 'A redirect already exists for this path.']);
        }
    }

    private function pagePathExists(string $path): bool
    {
        return Page::query()->get()->contains(fn (Page $page): bool => $page->path() === $path || $page->aliases()->where('path', $path)->exists());
    }
}
