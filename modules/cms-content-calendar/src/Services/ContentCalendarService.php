<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendar\Services;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentCalendar\Models\CalendarCampaign;
use Liberu\Cms\ContentCalendar\Models\CalendarItem;

final readonly class ContentCalendarService
{
    /** @return LengthAwarePaginator<int, CalendarItem> */
    public function items(?int $teamId, ?string $channel = null, ?string $site = null, int $perPage = 25): LengthAwarePaginator
    {
        $maximum = config('content-calendar.pagination.max', 100);

        return CalendarItem::query()->where('team_id', $teamId)->when($channel !== null, fn ($q) => $q->where('channel', $channel))->when($site !== null, fn ($q) => $q->where('site', $site))->orderBy('starts_at')->paginate(max(1, min($perPage, is_int($maximum) ? $maximum : 100)));
    }

    /** @param array<string, mixed> $data */
    public function campaign(array $data, ?int $teamId = null): CalendarCampaign
    {
        if (blank($data['name'] ?? null)) {
            throw ValidationException::withMessages(['name' => 'A campaign name is required.']);
        }

        $name = is_string($data['name'] ?? null) ? $data['name'] : '';

        return CalendarCampaign::query()->create([...$data, 'team_id' => $teamId, 'slug' => is_string($data['slug'] ?? null) ? $data['slug'] : (string) str($name)->slug()]);
    }

    /** @param array<string, mixed> $data */
    public function schedule(array $data, ?int $teamId = null): CalendarItem
    {
        if (blank($data['title'] ?? null)) {
            throw ValidationException::withMessages(['title' => 'A calendar title is required.']);
        }
        $startsAt = $this->parseDate($data['starts_at'] ?? null, 'starts_at');
        $deadlineAt = isset($data['deadline_at']) ? $this->parseDate($data['deadline_at'], 'deadline_at') : null;
        if ($deadlineAt instanceof Carbon && $deadlineAt->lessThan($startsAt)) {
            throw ValidationException::withMessages(['deadline_at' => 'A deadline cannot precede the scheduled start.']);
        }
        $channel = is_string($data['channel'] ?? null) ? $data['channel'] : null;
        $site = is_string($data['site'] ?? null) ? $data['site'] : null;
        if ($this->hasConflict($teamId, $channel, $site, $startsAt, $deadlineAt)) {
            throw ValidationException::withMessages(['starts_at' => 'The schedule conflicts with another calendar item.']);
        }

        return CalendarItem::query()->create([...$data, 'team_id' => $teamId, 'starts_at' => $startsAt, 'deadline_at' => $deadlineAt]);
    }

    public function reschedule(CalendarItem $item, string $startsAt, ?string $deadlineAt = null): CalendarItem
    {
        $start = $this->parseDate($startsAt, 'starts_at');
        $deadline = $deadlineAt === null ? null : $this->parseDate($deadlineAt, 'deadline_at');
        if ($deadline instanceof Carbon && $deadline->lessThan($start)) {
            throw ValidationException::withMessages(['deadline_at' => 'A deadline cannot precede the scheduled start.']);
        }
        if ($this->hasConflict($item->team_id, $item->channel, $item->site, $start, $deadline, $item->id)) {
            throw ValidationException::withMessages(['starts_at' => 'The rescheduled item conflicts with another calendar item.']);
        }
        $item->update(['starts_at' => $start, 'deadline_at' => $deadline]);

        $fresh = $item->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The calendar item could not be refreshed.');
        }

        return $fresh;
    }

    private function hasConflict(?int $teamId, ?string $channel, ?string $site, Carbon $start, ?Carbon $deadline, ?int $ignore = null): bool
    {
        return CalendarItem::query()->where('team_id', $teamId)->when($channel !== null, fn ($q) => $q->where('channel', $channel))->when($site !== null, fn ($q) => $q->where('site', $site))->when($ignore !== null, fn ($q) => $q->whereKeyNot($ignore))->where(function ($q) use ($start, $deadline): void {
            $q->whereBetween('starts_at', [$start, $deadline ?? $start])->orWhere(function ($nested) use ($start): void {
                $nested->whereNotNull('deadline_at')->where('deadline_at', '>=', $start);
            });
        })->exists();
    }

    private function parseDate(mixed $value, string $field): Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            throw ValidationException::withMessages([$field => 'A valid date and time is required.']);
        }

        try {
            return Carbon::parse($value);
        } catch (InvalidFormatException) {
            throw ValidationException::withMessages([$field => 'A valid date and time is required.']);
        }
    }
}
