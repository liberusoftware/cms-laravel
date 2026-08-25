<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditorApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\RichTextEditorApi\Http\RichTextController;

final class RichTextEditorApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('rich-text-editor-api', new ApiEndpoint('cms/rich-text-editor/sanitize', RichTextController::class, 'sanitize', 'cms.rich-text-editor.sanitize', 'POST'));
            $r->registerEndpoint('rich-text-editor-api', new ApiEndpoint('cms/rich-text-editor/prepare', RichTextController::class, 'prepare', 'cms.rich-text-editor.prepare', 'POST'));
            $r->registerEndpoint('rich-text-editor-api', new ApiEndpoint('cms/rich-text-editor/embed', RichTextController::class, 'embed', 'cms.rich-text-editor.embed', 'POST'));
        }
    }
}
