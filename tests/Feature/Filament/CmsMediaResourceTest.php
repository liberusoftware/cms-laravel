<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Liberu\Cms\Media\Models\Media;
use Liberu\Cms\MediaLibraryFilament\Resources\MediaResource;
use Liberu\Cms\MediaLibraryFilament\Resources\Pages\ListMedia;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['media.view', 'media.update', 'media.delete']);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);

    // Livewire::test() does not run Panel::boot(), so register the tenancy scope
    // and creation observer the way a real request would.
    MediaResource::registerTenancyModelGlobalScope($panel);
    MediaResource::observeTenancyModelCreation($panel);

    Filament::setTenant($this->team);
});

function makeMedia(array $overrides = []): Media
{
    return Media::create(array_merge([
        'disk' => 'public',
        'path' => 'media/example.jpg',
        'file_name' => 'example.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 2048,
        'folder' => null,
        'metadata' => [],
    ], $overrides));
}

it('renders the media list', function (): void {
    Livewire::test(ListMedia::class)->assertSuccessful();
});

it('lists media records', function (): void {
    $items = collect(range(1, 3))->map(fn (int $i): Media => makeMedia(['file_name' => "file-{$i}.jpg", 'path' => "media/file-{$i}.jpg"]));

    Livewire::test(ListMedia::class)->assertCanSeeTableRecords($items);
});

it('does not allow creating media directly', function (): void {
    expect(MediaResource::canCreate())->toBeFalse();
});

it('edits media metadata through the row action', function (): void {
    $media = makeMedia(['file_name' => 'old.jpg']);

    Livewire::test(ListMedia::class)
        ->callTableAction('edit', $media, ['file_name' => 'renamed.jpg']);

    expect($media->fresh()->file_name)->toBe('renamed.jpg');
});

it('deletes media through the row action', function (): void {
    $media = makeMedia();

    Livewire::test(ListMedia::class)->callTableAction('delete', $media);

    $this->assertModelMissing($media);
});

it('uploads a file through the header action', function (): void {
    Storage::fake('public');
    config()->set('cms-media.disk', 'public');
    config()->set('cms-media.max_size_kb', 20480);
    config()->set('cms-media.allowed_mime_types', ['image/jpeg', 'image/png']);

    Livewire::test(ListMedia::class)
        ->callAction('upload', [
            'file' => UploadedFile::fake()->image('new-upload.png'),
        ]);

    expect(Media::query()->where('file_name', 'new-upload.png')->exists())->toBeTrue();
});

it('ignores an upload action without a file', function (): void {
    Livewire::test(ListMedia::class)
        ->callAction('upload', ['folder' => 'empty']);

    expect(Media::query()->count())->toBe(0);
});

it('notifies when the upload action rejects a file', function (): void {
    Storage::fake('public');
    config()->set('cms-media.allowed_mime_types', ['image/jpeg']);

    Livewire::test(ListMedia::class)
        ->callAction('upload', ['file' => UploadedFile::fake()->create('script.php', 8, 'application/x-php')])
        ->assertNotified('Upload rejected.');

    expect(Media::query()->count())->toBe(0);
});

it('scopes media to the current tenant', function (): void {
    Filament::setTenant($this->team);
    $mine = makeMedia(['file_name' => 'mine.jpg', 'path' => 'media/mine.jpg']);

    $otherTeam = Team::factory()->create(['user_id' => $this->user->id]);
    Filament::setTenant($otherTeam);
    $theirs = makeMedia(['file_name' => 'theirs.jpg', 'path' => 'media/theirs.jpg']);

    Filament::setTenant($this->team);

    expect($mine->team_id)->toBe($this->team->id)
        ->and($theirs->team_id)->toBe($otherTeam->id);

    Livewire::test(ListMedia::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
