<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Liberu\Cms\Contracts\Events\Media\MediaUploaded;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Liberu\Cms\Media\Exceptions\InvalidUpload;
use Liberu\Cms\Media\Media\StoreUpload;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
});

it('stores a valid image, captures dimensions, and persists a record', function (): void {
    $media = app(StoreUpload::class)(UploadedFile::fake()->image('photo.jpg', 120, 80), 'articles');

    Storage::disk('public')->assertExists($media->path());

    expect($media->fileName())->toBe('photo.jpg')
        ->and($media->folder())->toBe('articles')
        ->and($media->mimeType())->toBe('image/jpeg')
        ->and($media->metadata())->toMatchArray(['width' => 120, 'height' => 80]);

    $this->assertDatabaseHas('cms_media', ['file_name' => 'photo.jpg', 'folder' => 'articles']);
});

it('broadcasts MediaUploaded after a successful upload', function (): void {
    Event::fake([MediaUploaded::class]);

    app(StoreUpload::class)(UploadedFile::fake()->image('banner.png'));

    Event::assertDispatched(MediaUploaded::class);
});

it('rejects a disallowed file type', function (): void {
    expect(fn () => app(StoreUpload::class)(UploadedFile::fake()->create('script.php', 8, 'application/x-php')))
        ->toThrow(InvalidUpload::class);

    expect(Storage::disk('public')->allFiles())->toBe([]);
});

it('rejects a file that exceeds the size limit', function (): void {
    config(['cms-media.max_size_kb' => 100]);

    expect(fn () => app(StoreUpload::class)(UploadedFile::fake()->create('huge.pdf', 500, 'application/pdf')))
        ->toThrow(InvalidUpload::class);
});

it('rejects an upload that PHP marked as invalid', function (): void {
    $file = new UploadedFile(__FILE__, 'invalid.php', 'application/x-php', UPLOAD_ERR_INI_SIZE, true);

    expect(fn () => app(StoreUpload::class)($file))->toThrow(InvalidUpload::class);
});

it('stores an image upload without dimensions when its metadata is unreadable', function (): void {
    $file = UploadedFile::fake()->create('not-an-image.jpg', 1, 'image/jpeg');

    $media = app(StoreUpload::class)($file);

    expect($media->metadata())->toBe([]);
});

it('rejects unsafe media folder paths', function (): void {
    expect(fn () => app(StoreUpload::class)(UploadedFile::fake()->image('photo.jpg'), '../outside'))
        ->toThrow(InvalidUpload::class)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

it('finds media and lists it by folder through the repository', function (): void {
    $a = app(StoreUpload::class)(UploadedFile::fake()->image('a.jpg'), 'gallery');
    app(StoreUpload::class)(UploadedFile::fake()->image('b.jpg'), 'other');

    $repository = app(MediaRepositoryInterface::class);

    expect($repository->find($a->mediaId())?->fileName())->toBe('a.jpg')
        ->and($repository->inFolder('gallery'))->toHaveCount(1)
        ->and($repository->inFolder('other'))->toHaveCount(1);
});

it('deletes a media item and its underlying file', function (): void {
    $media = app(StoreUpload::class)(UploadedFile::fake()->image('gone.jpg'));
    $repository = app(MediaRepositoryInterface::class);

    Storage::disk('public')->assertExists($media->path());

    expect($repository->delete($media->mediaId()))->toBeTrue();

    Storage::disk('public')->assertMissing($media->path());
    expect($repository->find($media->mediaId()))->toBeNull();
});
