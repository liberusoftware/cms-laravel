<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Copilot\Contracts\CopilotHandlerInterface;
use Liberu\Cms\Copilot\Services\CopilotService;

uses(RefreshDatabase::class);

it('bounds copilot requests by capability, is idempotent, and protects action confirmation', function (): void {
    $service = app(CopilotService::class);
    $service->registerHandler(new class implements CopilotHandlerInterface
    {
        public function capability(): string
        {
            return 'action-confirmation';
        }

        public function handle(string $prompt, array $input): array
        {
            return ['action' => 'publish', 'target' => $input['target']];
        }
    });
    $request = $service->submit(2, 'action-confirmation', 'Publish this page', ['target' => '42'], 'copilot-1');
    expect($service->submit(2, 'action-confirmation', 'changed', [], 'copilot-1')->getKey())->toBe($request->getKey());
    $pending = $service->execute($request);
    $token = $service->requireConfirmation($pending);
    $confirmed = $service->confirm($pending->fresh(), $token);
    expect($confirmed->status)->toBe('confirmed')->and($confirmed->confirmed_at)->not->toBeNull();
});

it('rejects unsupported capabilities and unapproved execution', function (): void {
    $service = app(CopilotService::class);
    expect(fn () => $service->submit(2, 'unknown', 'test'))->toThrow(ValidationException::class);
    $request = $service->submit(2, 'summary', 'Summarize');
    expect(fn () => $service->execute($request))->toThrow(ValidationException::class);
});
