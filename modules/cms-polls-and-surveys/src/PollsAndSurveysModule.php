<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveys;

use Liberu\Cms\Core\Module\AbstractModule;

final class PollsAndSurveysModule extends AbstractModule
{
    public function key(): string
    {
        return 'polls-and-surveys';
    }

    public function name(): string
    {
        return 'Polls and Surveys';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
