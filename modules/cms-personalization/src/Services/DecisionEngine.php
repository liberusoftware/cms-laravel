<?php

declare(strict_types=1);

namespace Liberu\Cms\Personalization\Services;

use Illuminate\Support\Arr;
use Liberu\Cms\Personalization\Models\Audience;
use Liberu\Cms\Personalization\Models\Decision;
use Liberu\Cms\Personalization\Models\Variant;

final class DecisionEngine
{
    /** @return array{variant: Variant|null, reason: string} */
    public function decide(string $audienceKey, array $context = [], ?string $subjectKey = null, bool $consent = false): array
    {
        $audience = Audience::query()->where('key', $audienceKey)->where('active', true)->first();
        $reason = 'audience_not_found';
        $variant = null;

        if ($audience !== null) {
            if ($audience->requires_consent && ! $consent) {
                $reason = 'consent_required';
            } elseif (! $this->matches($audience->rules, $context)) {
                $reason = 'ineligible';
            } else {
                $variant = $audience->variants()->where('active', true)->orderByDesc('priority')->get()
                    ->first(fn (Variant $candidate): bool => $candidate->fallback || $this->outsideHoldout($candidate, $subjectKey));
                $reason = $variant === null ? 'no_variant' : ($variant->fallback ? 'fallback' : 'eligible');
            }
        }

        Decision::create(['audience_key' => $audienceKey, 'variant_key' => $variant?->key, 'subject_key' => $subjectKey === null ? null : hash('sha256', $subjectKey), 'context' => $this->evidenceContext($context), 'reason' => $reason, 'team_id' => $audience?->team_id]);

        return ['variant' => $variant, 'reason' => $reason];
    }

    private function matches(array $rules, array $context): bool
    {
        foreach ($rules as $key => $expected) {
            $actual = Arr::get($context, (string) $key);
            if (is_array($expected) ? ! in_array($actual, $expected, true) : $actual !== $expected) {
                return false;
            }
        }

        return true;
    }

    private function outsideHoldout(Variant $variant, ?string $subjectKey): bool
    {
        if ($variant->holdout_percent <= 0 || $subjectKey === null) {
            return true;
        }

        return (hexdec(substr(hash('sha256', $variant->audience_id.':'.$subjectKey), 0, 8)) % 100) >= $variant->holdout_percent;
    }

    /** @return array<string, scalar|null> */
    private function evidenceContext(array $context): array
    {
        $allowed = ['plan', 'country', 'locale', 'device', 'channel', 'consent'];
        $evidence = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $context) && (is_scalar($context[$key]) || $context[$key] === null)) {
                $evidence[$key] = $context[$key];
            }
        }

        return $evidence;
    }
}
