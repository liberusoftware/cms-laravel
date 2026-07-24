<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Api;

/**
 * A single Delivery API endpoint a content module contributes to the API.
 *
 * The module names the URI (relative to the version prefix), the controller
 * class and method that serve it, and a route-name suffix. The API package
 * reads these descriptors to define the versioned route group, so it never
 * imports the module's controller by name.
 */
final class ApiEndpoint
{
    /**
     * @param  string  $uri  Path relative to the version prefix, e.g. "pages" or "pages/{slug}".
     * @param  class-string  $controller  The controller class serving the endpoint.
     * @param  string  $action  The controller method to invoke.
     * @param  string  $name  Route-name suffix, e.g. "pages.index".
     * @param  string  $method  The HTTP verb.
     */
    public function __construct(
        public string $uri,
        public string $controller,
        public string $action,
        public string $name,
        public string $method = 'GET',
    ) {}
}
