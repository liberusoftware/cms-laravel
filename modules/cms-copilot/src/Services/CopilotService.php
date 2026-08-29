<?php

declare(strict_types=1);

namespace Liberu\Cms\Copilot\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Copilot\Contracts\CopilotHandlerInterface;
use Liberu\Cms\Copilot\Models\CopilotRequest;

final class CopilotService
{
    private const CAPABILITIES = ['search', 'summary', 'draft', 'transform', 'metadata', 'internal-links', 'action-confirmation'];

    /** @var array<string, CopilotHandlerInterface> */
    private array $handlers = [];

    public function submit(?int $teamId, string $capability, string $prompt, array $input = [], ?string $idempotencyKey = null): CopilotRequest
    {
        if (! in_array($capability, self::CAPABILITIES, true)) {
            throw ValidationException::withMessages(['capability' => 'The Copilot capability is not supported.']);
        }
        if (trim($prompt) === '' || strlen($prompt) > 10000) {
            throw ValidationException::withMessages(['prompt' => 'A prompt of 1 to 10,000 characters is required.']);
        }
        $key = trim($idempotencyKey ?? (string) Str::uuid());
        if ($key === '') {
            throw ValidationException::withMessages(['idempotency_key' => 'An idempotency key is required.']);
        }

        return CopilotRequest::query()->firstOrCreate(['team_id' => $teamId, 'idempotency_key' => $key], ['capability' => $capability, 'prompt' => $prompt, 'input' => $input, 'status' => 'pending']);
    }

    public function execute(CopilotRequest $request): CopilotRequest
    {
        if ($request->status !== 'pending') {
            return $request->fresh();
        }
        if (! isset($this->handlers[$request->capability])) {
            throw ValidationException::withMessages(['capability' => 'No approved Copilot handler is registered for this capability.']);
        }
        try {
            $result = $this->handlers[$request->capability]->handle($request->prompt, $request->input ?? []);
            $request->update(['result' => $result, 'status' => $request->capability === 'action-confirmation' ? 'awaiting_confirmation' : 'completed']);
        } catch (\Throwable $exception) {
            $request->update(['status' => 'failed', 'failure_reason' => 'Copilot execution failed.']);
            throw $exception;
        }

        return $request->fresh();
    }

    public function requireConfirmation(CopilotRequest $request): string
    {
        if ($request->status !== 'awaiting_confirmation') {
            throw ValidationException::withMessages(['request' => 'Only an action awaiting confirmation can be confirmed.']);
        }
        $token = Str::random(40);
        $request->update(['confirmation_hash' => hash('sha256', $token)]);

        return $token;
    }

    public function confirm(CopilotRequest $request, string $token): CopilotRequest
    {
        if ($request->status !== 'awaiting_confirmation' || ! $request->confirmation_hash || ! hash_equals($request->confirmation_hash, hash('sha256', $token))) {
            throw ValidationException::withMessages(['confirmation' => 'The confirmation token is invalid or expired.']);
        }
        $request->update(['status' => 'confirmed', 'confirmed_at' => now(), 'confirmation_hash' => null]);

        return $request->fresh();
    }

    public function registerHandler(CopilotHandlerInterface $handler): void
    {
        if (! in_array($handler->capability(), self::CAPABILITIES, true)) {
            throw ValidationException::withMessages(['capability' => 'The Copilot capability is not supported.']);
        }
        $this->handlers[$handler->capability()] = $handler;
    }
}
