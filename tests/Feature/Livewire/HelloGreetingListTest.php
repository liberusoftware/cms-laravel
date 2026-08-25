<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Hello\Models\Greeting;
use Liberu\Cms\HelloLivewire\Livewire\GreetingList;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('the hello livewire adapter renders paginated greetings', function (): void {
    Greeting::create(['name' => 'Ada', 'message' => 'Hello Ada']);

    Livewire::test(GreetingList::class)
        ->assertSee('Ada')
        ->assertSee('Hello Ada')
        ->assertSet('perPage', 10);
});

test('the hello livewire adapter clamps page size and resets pagination', function (): void {
    Livewire::test(GreetingList::class)
        ->set('perPage', 100)
        ->assertSet('perPage', 50)
        ->set('perPage', 0)
        ->assertSet('perPage', 1);
});
