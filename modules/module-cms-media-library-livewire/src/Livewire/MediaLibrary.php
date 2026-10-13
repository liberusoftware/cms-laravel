<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaLibraryLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Livewire\Component;

final class MediaLibrary extends Component
{
    public string $folder = '';

    public int $pageSize = 25;

    public function render(MediaRepositoryInterface $media): View
    {
        if (auth()->user()?->can('media.view') !== true) {
            return view('cms-media-library-livewire::media-library', ['items' => []]);
        }

        $folder = trim($this->folder);
        $size = max(1, min($this->pageSize, 100));
        $items = array_slice(iterator_to_array($media->inFolder($folder === '' ? null : $folder)), 0, $size);

        return view('cms-media-library-livewire::media-library', [
            'items' => array_values(array_filter($items, static fn (mixed $item): bool => $item instanceof MediaItemInterface)),
        ]);
    }
}
