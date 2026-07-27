<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Hello\Http\Resources\GreetingResource;
use Liberu\Cms\Hello\Models\Greeting;

/**
 * Delivery API endpoint for the reference extension: lists greetings as Eloquent
 * API Resources. Registered via ApiResourceRegistryInterface, so it inherits the
 * API group's auth, throttle, and JSON handling.
 */
final class GreetingApiController
{
    public function index(): AnonymousResourceCollection
    {
        return GreetingResource::collection(Greeting::query()->get());
    }
}
