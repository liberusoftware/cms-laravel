<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditorApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BlockEditorApi\Http\BlockEditorController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class BlockEditorApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('block-editor-api', new ApiEndpoint('cms/block-editor/documents/{subjectType}/{subjectId}', BlockEditorController::class, 'save', 'cms.block-editor.documents.save', 'PUT', ['abilities:content:update']));
        $registry->registerEndpoint('block-editor-api', new ApiEndpoint('cms/block-editor/documents/{document}/lock', BlockEditorController::class, 'lock', 'cms.block-editor.documents.lock', 'POST', ['abilities:content:lock']));
        $registry->registerEndpoint('block-editor-api', new ApiEndpoint('cms/block-editor/patterns', BlockEditorController::class, 'pattern', 'cms.block-editor.patterns.create', 'POST', ['abilities:content:create']));
    }
}
