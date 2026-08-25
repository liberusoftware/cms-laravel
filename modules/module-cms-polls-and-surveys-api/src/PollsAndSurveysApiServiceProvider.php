<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\PollsAndSurveysApi\Http\PollController;

final class PollsAndSurveysApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys', PollController::class, 'index', 'cms.polls.index'));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys', PollController::class, 'create', 'cms.polls.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}', PollController::class, 'show', 'cms.polls.show'));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}', PollController::class, 'update', 'cms.polls.update', 'PATCH', ['abilities:content:write']));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}', PollController::class, 'destroy', 'cms.polls.destroy', 'DELETE', ['abilities:content:write']));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}/responses', PollController::class, 'store', 'cms.polls.responses.store', 'POST'));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}/results', PollController::class, 'results', 'cms.polls.results', 'GET', ['abilities:content:read']));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}/export', PollController::class, 'export', 'cms.polls.export', 'GET', ['abilities:content:export']));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}/responses/{response}', PollController::class, 'eraseResponse', 'cms.polls.responses.erase', 'DELETE', ['abilities:content:write']));
        }
    }
}
