<?php

declare(strict_types=1);

namespace Liberu\Cms\Experimentation\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Experimentation\Models\Experiment;
use Liberu\Cms\Experimentation\Models\ExperimentObservation;
use Liberu\Cms\Experimentation\Models\ExperimentPromotion;
use Liberu\Cms\Experimentation\Models\ExperimentVariant;

final class ExperimentationService
{
    public function create(array $attributes, ?int $teamId = null): Experiment
    {
        $key = trim((string) ($attributes['key'] ?? ''));
        $name = trim((string) ($attributes['name'] ?? ''));
        $variants = $attributes['variants'] ?? [];
        if ($key === '' || $name === '' || ! is_array($variants) || count($variants) < 2) {
            throw ValidationException::withMessages(['experiment' => 'An experiment requires a key, name, and at least two variants.']);
        }
        $weights = array_map(static fn ($variant): int => (int) ($variant['weight'] ?? 0), $variants);
        if (array_sum($weights) !== 100 || min($weights) < 1) {
            throw ValidationException::withMessages(['variants' => 'Variant weights must be positive and total 100.']);
        }
        if (! in_array($attributes['type'] ?? 'ab', ['ab', 'multivariate'], true)) {
            throw ValidationException::withMessages(['type' => 'Experiment type must be ab or multivariate.']);
        }

        return DB::transaction(function () use ($attributes, $key, $name, $variants, $teamId): Experiment {
            $experiment = Experiment::query()->updateOrCreate(['key' => $key, 'team_id' => $teamId], ['name' => $name, 'type' => $attributes['type'] ?? 'ab', 'status' => 'draft', 'allocation_percentage' => $attributes['allocation_percentage'] ?? 100, 'goals' => $attributes['goals'] ?? [], 'guardrails' => $attributes['guardrails'] ?? [], 'analysis_policy' => $attributes['analysis_policy'] ?? [], 'team_id' => $teamId]);
            $experiment->variants()->delete();
            foreach ($variants as $variant) {
                $variantKey = trim((string) ($variant['key'] ?? ''));
                if ($variantKey === '') {
                    throw ValidationException::withMessages(['variants' => 'Every variant needs a key.']);
                }
                $experiment->variants()->create(['key' => $variantKey, 'name' => $variant['name'] ?? $variantKey, 'content' => $variant['content'] ?? [], 'weight' => (int) $variant['weight']]);
            }

            return $experiment->load('variants');
        });
    }

    public function start(Experiment $experiment): Experiment
    {
        if ($experiment->variants()->count() < 2 || $experiment->allocation_percentage < 1) {
            throw ValidationException::withMessages(['experiment' => 'An experiment must have at least two allocatable variants.']);
        }
        $experiment->update(['status' => 'running']);

        return $experiment->refresh();
    }

    public function stop(Experiment $experiment): Experiment
    {
        if ($experiment->status !== 'running') {
            throw ValidationException::withMessages(['experiment' => 'Only running experiments can be stopped.']);
        }
        $experiment->update(['status' => 'stopped']);

        return $experiment->refresh();
    }

    public function allocate(Experiment $experiment, string $subjectKey): ?ExperimentVariant
    {
        if ($experiment->status !== 'running' || trim($subjectKey) === '') {
            return null;
        }
        $bucket = hexdec(substr(hash('sha256', $experiment->key.'|'.$subjectKey), 0, 8)) % 100;
        if ($bucket >= $experiment->allocation_percentage) {
            return null;
        }
        $cursor = 0;
        foreach ($experiment->variants as $variant) {
            $cursor += $variant->weight;
            if ($bucket < $cursor) {
                return $variant;
            }
        }

        return $experiment->variants->last();
    }

    public function observe(Experiment $experiment, ExperimentVariant $variant, string $subjectKey, ?string $goalKey, float $value = 1): ExperimentObservation
    {
        if ($variant->experiment_id !== $experiment->id || trim($subjectKey) === '' || $value < 0) {
            throw ValidationException::withMessages(['observation' => 'Observation subject, variant, and value are invalid.']);
        }

        return ExperimentObservation::query()->updateOrCreate(['experiment_id' => $experiment->id, 'variant_id' => $variant->id, 'subject_key' => $subjectKey, 'goal_key' => $goalKey], ['value' => $value, 'observed_at' => now()]);
    }

    public function promote(Experiment $experiment, ExperimentVariant $variant, ?string $reason = null, ?string $actorType = null, int|string|null $actorId = null): Experiment
    {
        if ($variant->experiment_id !== $experiment->id || ! in_array($experiment->status, ['running', 'stopped'], true)) {
            throw ValidationException::withMessages(['promotion' => 'Only a valid running or stopped experiment variant may be promoted.']);
        }

        return DB::transaction(function () use ($experiment, $variant, $reason, $actorType, $actorId): Experiment {
            ExperimentPromotion::query()->create(['experiment_id' => $experiment->id, 'variant_id' => $variant->id, 'actor_type' => $actorType, 'actor_id' => $actorId === null ? null : (string) $actorId, 'reason' => $reason, 'promoted_at' => now()]);
            $experiment->update(['status' => 'promoted', 'winner_variant_key' => $variant->key]);

            return $experiment->refresh();
        });
    }
}
