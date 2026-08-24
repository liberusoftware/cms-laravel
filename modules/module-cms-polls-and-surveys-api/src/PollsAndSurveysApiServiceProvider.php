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
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}', PollController::class, 'show', 'cms.polls.show'));
            $registry->registerEndpoint('polls-and-surveys-api', new ApiEndpoint('cms/polls-and-surveys/{key}/responses', PollController::class, 'store', 'cms.polls.responses.store', 'POST'));
        }
    }
}
