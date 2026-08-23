<?php

declare(strict_types=1);

use Liberu\Cms\Hello\HelloModule;
use Liberu\Cms\Hello\Services\Greeter;

it('describes the hello module', function (): void {
    $module = new HelloModule;

    expect($module->key())->toBe('hello')
        ->and($module->name())->toBe('Hello')
        ->and($module->version())->not->toBeEmpty()
        ->and($module->isFoundational())->toBeFalse()
        ->and($module->dependencies())->toBe([]);
});

it('greets using its configured template', function (): void {
    $greeter = new Greeter('Hi, :name!');

    expect($greeter->greet('ada'))->toBe('Hi, ada!');
});
