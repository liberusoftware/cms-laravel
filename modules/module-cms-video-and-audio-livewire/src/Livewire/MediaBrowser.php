<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\VideoAndAudio\Actions\MediaManagementService;
use Liberu\Cms\VideoAndAudio\Queries\MediaAssetQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class MediaBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $kind = '';

    public int $perPage = 15;

    public ?array $playback = null;

    public function updatedSearch(): void
    {
        $this->search = substr(trim($this->search), 0, 255);
        $this->resetPage();
    }

    public function updatedKind(): void
    {
        $this->kind = in_array($this->kind, ['video', 'audio', ''], true) ? $this->kind : '';
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, min(100, $this->perPage));
        $this->resetPage();
    }

    public function play(string $publicId, MediaAssetQuery $assets, MediaManagementService $service): void
    {
        if ($asset = $assets->find($publicId)) {
            try {
                $metadata = $service->playback($asset);
                $this->playback = ['title' => $metadata->title, 'stream_uri' => $metadata->streamUri, 'poster_uri' => $metadata->posterUri, 'tracks' => $metadata->tracks];
            } catch (\Throwable) {
                $this->playback = null;
            }
        }
    }

    public function render(MediaAssetQuery $assets): View
    {
        return view('module-cms-video-and-audio-livewire::livewire.media-browser', ['assets' => $assets->paginate($this->perPage, $this->search, $this->kind ?: null)]);
    }
}
