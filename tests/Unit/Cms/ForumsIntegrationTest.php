<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ForumsIntegration\Contracts\ForumProvider;
use Liberu\Cms\ForumsIntegration\Services\ForumProviderRegistry;
use Liberu\Cms\ForumsIntegration\Services\ForumsIntegrationService;

uses(RefreshDatabase::class);

it('links references and delegates recent discussions, moderation, and SSO through a provider contract', function (): void {
    $provider = new class implements ForumProvider
    {
        public function recentDiscussions(?int $teamId = null, int $limit = 10): array
        {
            return [['id' => 'thread-1', 'title' => 'Welcome']];
        }

        public function moderationUrl(string $externalType, string $externalId): ?string
        {
            return 'https://forum.test/moderate/'.$externalId;
        }

        public function ssoContext(string $subjectKey, ?int $teamId = null): array
        {
            return ['subject' => $subjectKey, 'team' => $teamId];
        }
    };
    $registry = app(ForumProviderRegistry::class);
    $registry->register('community', $provider);
    $service = app(ForumsIntegrationService::class);
    $reference = $service->link('community', 'thread', 'thread-1', 'https://forum.test/t/thread-1', 7);

    expect($service->recent('community', 7))->toHaveCount(1)->and($service->moderationUrl($reference, 7))->toContain('thread-1')->and($service->sso('community', 'user-1', 7)['subject'])->toBe('user-1');
});

it('rejects unsafe links and cross-tenant moderation access', function (): void {
    $registry = app(ForumProviderRegistry::class);
    $registry->register('community', new class implements ForumProvider
    {
        public function recentDiscussions(?int $teamId = null, int $limit = 10): array
        {
            return [];
        }

        public function moderationUrl(string $externalType, string $externalId): ?string
        {
            return null;
        }

        public function ssoContext(string $subjectKey, ?int $teamId = null): array
        {
            return [];
        }
    });
    $service = app(ForumsIntegrationService::class);
    expect(fn () => $service->link('community', 'thread', 'x', 'http://unsafe.test', 7))->toThrow(ValidationException::class);
    $reference = $service->link('community', 'thread', 'x', null, 7);
    expect(fn () => $service->moderationUrl($reference, 8))->toThrow(ValidationException::class);
});
