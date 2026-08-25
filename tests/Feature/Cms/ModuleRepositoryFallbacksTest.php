<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Forms\Models\Form;
use Liberu\Cms\Forms\Support\SubmissionValidator;
use Liberu\Cms\Media\Media\MediaRepository;
use Liberu\Cms\Menus\Repositories\MenuRepository;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Posts\Models\Tag;

uses(RefreshDatabase::class);

it('returns safe nulls for missing media and menu records', function (): void {
    expect((new MediaRepository)->delete(-1))->toBeFalse()
        ->and((new MenuRepository)->find(-1))->toBeNull()
        ->and((new MenuRepository)->forLocation('__missing__'))->toBeNull();
});

it('builds tenant-neutral relationships and ignores the current record in slug checks', function (): void {
    $tag = new Tag;
    $page = new Page(['slug' => 'same']);
    $page->exists = true;
    $page->setAttribute($page->getKeyName(), 999);

    expect($tag->posts())->toBeInstanceOf(BelongsToMany::class)
        ->and(Slugger::unique($page, 'same'))->toBe('same');
});

it('drops unnamed form fields while retaining the named field label fallback', function (): void {
    $form = new Form([
        'fields' => [
            ['name' => '', 'label' => 'Ignored', 'type' => 'text'],
            ['name' => 'message', 'label' => '', 'type' => 'text'],
        ],
    ]);

    expect(app(SubmissionValidator::class)->validate($form, ['message' => 'hello']))
        ->toBe(['message' => 'hello']);
});

it('runs the permission synchronization command successfully', function (): void {
    $this->artisan('cms:sync-permissions')
        ->assertExitCode(0);
});
