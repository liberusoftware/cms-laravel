<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistant\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ExperienceAssistant\Models\ExperienceSuggestion;

final class ExperienceAssistantService
{
    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $constraints
     */
    public function suggest(string $surface, array $definition, array $constraints = [], ?int $teamId = null): ExperienceSuggestion
    {
        if (trim($surface) === '' || strlen($surface) > 180 || $definition === []) {
            throw ValidationException::withMessages(['suggestion' => 'A surface and block definition are required.']);
        }
        $diagnostics = $this->checkDefinition($definition, $constraints);

        return ExperienceSuggestion::query()->create(['public_id' => (string) Str::uuid(), 'team_id' => $teamId, 'surface' => $surface, 'definition' => $definition, 'constraints' => $constraints, 'diagnostics' => $diagnostics, 'status' => 'pending']);
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $constraints
     * @return array<string, mixed>
     */
    public function check(array $definition, array $constraints = []): array
    {
        return $this->checkDefinition($definition, $constraints);
    }

    public function approve(ExperienceSuggestion $suggestion, string $reviewerKey, ?int $teamId = null): ExperienceSuggestion
    {
        if ($suggestion->team_id !== $teamId) {
            throw ValidationException::withMessages(['team_id' => 'The suggestion belongs to another tenant.']);
        }
        if (trim($reviewerKey) === '') {
            throw ValidationException::withMessages(['reviewer_key' => 'A reviewer is required.']);
        }
        $diagnostics = $suggestion->getAttribute('diagnostics');
        $errors = is_array($diagnostics) && is_array($diagnostics['errors'] ?? null) ? $diagnostics['errors'] : ['invalid diagnostics'];
        if ($errors !== []) {
            throw ValidationException::withMessages(['suggestion' => 'Suggestions with blocking diagnostics cannot be approved.']);
        }
        $suggestion->update(['status' => 'approved', 'reviewer_key' => $reviewerKey, 'approved_at' => now()]);

        return $suggestion->refresh();
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $constraints
     * @return array<string, mixed>
     */
    private function checkDefinition(array $definition, array $constraints): array
    {
        $errors = [];
        $warnings = [];
        if (! array_key_exists('blocks', $definition) || ! is_array($definition['blocks'])) {
            $errors[] = 'blocks are required';
        }
        if (($constraints['max_blocks'] ?? null) !== null && is_int($constraints['max_blocks']) && is_array($definition['blocks'] ?? null) && count($definition['blocks']) > $constraints['max_blocks']) {
            $errors[] = 'block limit exceeded';
        }
        if (($definition['contrast_ratio'] ?? null) !== null && is_numeric($definition['contrast_ratio']) && (float) $definition['contrast_ratio'] < 4.5) {
            $errors[] = 'contrast ratio is below WCAG AA text guidance';
        }
        if (! array_key_exists('mobile', $definition)) {
            $warnings[] = 'mobile layout is not defined';
        }

        return ['errors' => $errors, 'warnings' => $warnings, 'checked_at' => now()->toAtomString()];
    }
}
