<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\OpenApi;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

/**
 * Builds the OpenAPI 3 description of the Delivery API directly from the live
 * router. Because the paths are derived from the same registered routes the
 * application serves, the document cannot drift out of sync with them: adding or
 * removing an endpoint changes the spec automatically. Per-operation security,
 * parameters, and error shapes are inferred from each route's method, name, and
 * middleware. This is a deliberate choice over annotation-based generation (which
 * would re-describe every route by hand and still drift).
 */
final readonly class OpenApiGenerator
{
    private const string PREFIX = 'api/v1';

    public function __construct(private Router $router) {}

    /**
     * @return array<string, mixed>
     */
    public function generate(): array
    {
        $appUrl = config('app.url');

        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Liberu CMS Delivery API',
                'version' => 'v1',
                'description' => 'Headless Delivery API for Liberu CMS. Authenticate with a Delivery '
                    .'token (Sanctum bearer) issued to a Team; the token scopes every read and write '
                    .'to that Team (tenant). Write endpoints additionally require the `content:write` '
                    .'ability. List endpoints are paginated via `page` and `per_page` (capped by '
                    .'configuration). Errors use the standard shapes: 401 (no/invalid token), 403 '
                    .'(missing ability or invalid/expired preview signature), 404 (absent or '
                    .'cross-tenant), 422 (validation), 429 (rate limit, with Retry-After). Preview '
                    .'links are public but signed and expiring, so they carry no bearer token.',
            ],
            'servers' => [
                ['url' => rtrim(is_string($appUrl) ? $appUrl : '', '/')],
            ],
            'components' => $this->components(),
            'paths' => $this->paths(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function components(): array
    {
        $error = [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string'],
            ],
        ];

        $validationError = [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string'],
                'errors' => [
                    'type' => 'object',
                    'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']],
                ],
            ],
        ];

        return [
            'securitySchemes' => [
                'sanctum' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'description' => 'A Delivery API token issued to a Team via `cms-api:issue-token`.',
                ],
            ],
            'schemas' => [
                'Error' => $error,
                'ValidationError' => $validationError,
            ],
            'responses' => [
                'Unauthorized' => $this->errorResponse('Missing or invalid Delivery token.', 'Error'),
                'Forbidden' => $this->errorResponse('The token lacks the required ability, or the preview signature is invalid or expired.', 'Error'),
                'NotFound' => $this->errorResponse('The item does not exist for the current tenant.', 'Error'),
                'UnprocessableEntity' => $this->errorResponse('The request failed validation.', 'ValidationError'),
                'TooManyRequests' => $this->errorResponse('Rate limit exceeded; see the Retry-After header.', 'Error'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function errorResponse(string $description, string $schema): array
    {
        return [
            'description' => $description,
            'content' => [
                'application/json' => [
                    'schema' => ['$ref' => '#/components/schemas/'.$schema],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function paths(): array
    {
        $paths = [];

        foreach ($this->router->getRoutes()->getRoutes() as $route) {
            $uri = $route->uri();

            if (! str_starts_with($uri, self::PREFIX)) {
                continue;
            }

            $path = '/'.str_replace('?}', '}', $uri);

            foreach ($route->methods() as $method) {
                if (! is_string($method) || in_array($method, ['HEAD', 'OPTIONS'], true)) {
                    continue;
                }

                $paths[$path][strtolower($method)] = $this->operation($route, $method, $uri);
            }
        }

        ksort($paths);

        return $paths;
    }

    /**
     * @return array<string, mixed>
     */
    private function operation(Route $route, string $method, string $uri): array
    {
        $name = $route->getName() ?? $method.' '.$uri;
        $isPreview = $name === 'cms-api.preview';
        $isSpec = str_ends_with($uri, 'openapi.json');
        $isPublic = $isPreview || $isSpec;
        $isWrite = $method !== 'GET';
        $hasPathParams = str_contains($uri, '{');

        $operation = [
            'operationId' => Str::of($name)->replace(['cms-api.', 'cms.'], '')->replace('.', '_')->toString(),
            'summary' => Str::of($name)->replace(['cms-api.', 'cms.'], '')->replace(['.', '-'], ' ')->title()->toString(),
            'tags' => [$this->tag($name)],
            'security' => $isPublic ? [] : [['sanctum' => []]],
            'responses' => $this->responses($method, $isPublic, $isWrite, $hasPathParams, $isPreview),
        ];

        if ($isWrite) {
            $operation['description'] = 'Requires the `content:write` ability.';
        }

        $parameters = $this->parameters($uri, $name);

        if ($parameters !== []) {
            $operation['parameters'] = $parameters;
        }

        return $operation;
    }

    private function tag(string $name): string
    {
        $segments = explode('.', $name);

        return $segments[1] ?? $segments[0];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parameters(string $uri, string $name): array
    {
        $parameters = [];

        preg_match_all('/\{(\w+)\??\}/', $uri, $matches);

        foreach ($matches[1] as $param) {
            $parameters[] = [
                'name' => $param,
                'in' => 'path',
                'required' => true,
                'schema' => ['type' => $param === 'id' ? 'integer' : 'string'],
            ];
        }

        if (str_ends_with($name, '.index')) {
            $parameters[] = $this->queryParam('page', 'integer', 'Page number.');
            $parameters[] = $this->queryParam('per_page', 'integer', 'Items per page (capped by configuration).');
        }

        if ($name === 'cms-api.posts.index') {
            $parameters[] = $this->queryParam('category', 'string', 'Filter by category slug.');
            $parameters[] = $this->queryParam('tag', 'string', 'Filter by tag slug.');
        }

        if ($name === 'cms-api.search.index') {
            $parameters[] = $this->queryParam('q', 'string', 'The search query.', true);
        }

        return $parameters;
    }

    /**
     * @return array<string, mixed>
     */
    private function queryParam(string $name, string $type, string $description, bool $required = false): array
    {
        return [
            'name' => $name,
            'in' => 'query',
            'required' => $required,
            'description' => $description,
            'schema' => ['type' => $type],
        ];
    }

    /**
     * @return array<int|string, mixed>
     */
    private function responses(string $method, bool $isPublic, bool $isWrite, bool $hasPathParams, bool $isPreview): array
    {
        $responses = [];

        $responses += match ($method) {
            'POST' => ['201' => ['description' => 'Created.', 'content' => $this->jsonObject()]],
            'DELETE' => ['204' => ['description' => 'Deleted.']],
            default => ['200' => ['description' => 'Successful response.', 'content' => $this->jsonObject()]],
        };

        if (! $isPublic) {
            $responses['401'] = ['$ref' => '#/components/responses/Unauthorized'];
        }

        if ($isWrite || $isPreview) {
            $responses['403'] = ['$ref' => '#/components/responses/Forbidden'];
        }

        if ($hasPathParams) {
            $responses['404'] = ['$ref' => '#/components/responses/NotFound'];
        }

        if ($isWrite) {
            $responses['422'] = ['$ref' => '#/components/responses/UnprocessableEntity'];
        }

        if (! $isPublic) {
            $responses['429'] = ['$ref' => '#/components/responses/TooManyRequests'];
        }

        return $responses;
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonObject(): array
    {
        return [
            'application/json' => [
                'schema' => ['type' => 'object'],
            ],
        ];
    }
}
