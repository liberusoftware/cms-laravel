<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\WebDelivery\Actions\WebDeliveryService;
use Livewire\Component;

final class RoutePreview extends Component
{
    public string $path = '';
    public string $previewToken = '';
    public ?array $result = null;

    public function mount(string $path = '/'): void { $this->path = trim($path); }

    public function resolve(WebDeliveryService $delivery): void
    {
        if ($this->path === '' || strlen($this->path) > 2048) throw ValidationException::withMessages(['path' => 'A valid delivery path is required.']);
        $result = $delivery->render($this->path, $this->previewToken ?: null);
        $this->result = ['status' => $result->status, 'body' => $result->body, 'metadata' => $result->metadata, 'canonical_url' => $result->canonicalUrl, 'redirect_url' => $result->redirectUrl, 'preview' => $result->preview];
    }

    public function render(): View { return view('module-cms-web-delivery-livewire::livewire.route-preview'); }
}
