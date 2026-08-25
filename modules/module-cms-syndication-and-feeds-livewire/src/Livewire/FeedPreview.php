<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\SyndicationAndFeeds\Models\Feed;
use Liberu\Cms\SyndicationAndFeeds\Services\FeedService;
use Livewire\Component;

final class FeedPreview extends Component
{
    public string $feedKey = '';

    public function render(FeedService $service): View
    {
        $feed = $this->feedKey === '' ? null : Feed::query()->where('key', $this->feedKey)->where('active', true)->first();

        return view('cms-syndication-and-feeds-livewire::feed-preview', ['body' => $feed ? $service->render($feed) : null]);
    }
}
