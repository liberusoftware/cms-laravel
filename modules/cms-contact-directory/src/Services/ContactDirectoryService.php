<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContactDirectory\Models\Contact;
use Liberu\Cms\ContactDirectory\Models\ContactCategory;
use Liberu\Cms\ContactDirectory\Models\ContactForm;
use Liberu\Cms\ContactDirectory\Models\ContactLocation;

final readonly class ContactDirectoryService
{
    public function contacts(?int $teamId, bool $publicOnly = false, int $perPage = 25): LengthAwarePaginator
    {
        return Contact::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($publicOnly, fn ($q) => $q->where('is_public', true))->orderBy('name')->paginate(max(1, min($perPage, (int) config('contact-directory.pagination.max', 100))));
    }

    public function saveContact(array $data, ?int $teamId = null): Contact
    {
        if (blank($data['name'] ?? null)) {
            throw ValidationException::withMessages(['name' => 'A contact name is required.']);
        }
        if (filled($data['email'] ?? null) && ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['email' => 'The email address is invalid.']);
        }
        $this->validateTenantReference(ContactCategory::class, $data['category_id'] ?? null, $teamId, 'category_id');
        $this->validateTenantReference(ContactLocation::class, $data['location_id'] ?? null, $teamId, 'location_id');

        return Contact::query()->create([...$data, 'team_id' => $teamId]);
    }

    public function category(array $data, ?int $teamId = null): ContactCategory
    {
        if (blank($data['name'] ?? null)) {
            throw ValidationException::withMessages(['name' => 'A category name is required.']);
        }

        return ContactCategory::query()->create([...$data, 'team_id' => $teamId, 'slug' => $data['slug'] ?? (string) str($data['name'])->slug()]);
    }

    public function location(array $data, ?int $teamId = null): ContactLocation
    {
        if (blank($data['name'] ?? null)) {
            throw ValidationException::withMessages(['name' => 'A location name is required.']);
        }

        return ContactLocation::query()->create([...$data, 'team_id' => $teamId]);
    }

    public function form(array $data, ?int $teamId = null): ContactForm
    {
        if (blank($data['name'] ?? null) || ! is_array($data['schema'] ?? null)) {
            throw ValidationException::withMessages(['form' => 'A form name and schema are required.']);
        }

        return ContactForm::query()->create([...$data, 'team_id' => $teamId, 'is_active' => $data['is_active'] ?? true]);
    }

    private function validateTenantReference(string $model, mixed $id, ?int $teamId, string $field): void
    {
        if ($id === null) {
            return;
        }

        if (! $model::query()->whereKey($id)->where('team_id', $teamId)->exists()) {
            throw ValidationException::withMessages([$field => 'The selected record must belong to the same tenant.']);
        }
    }
}
