<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperationsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\FormOperations\Models\OperationalSubmission;
use LogicException;

final class OperationalSubmissionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof OperationalSubmission) {
            throw new LogicException('OperationalSubmissionResource requires an OperationalSubmission instance.');
        }
        $submission = $this->resource;

        return ['id' => $submission->public_id, 'type' => 'cms-form-operation-submissions', 'form_id' => $submission->form_id, 'status' => $submission->status, 'consented_at' => $submission->consented_at->toISOString(), 'retention_until' => $submission->retention_until?->toISOString(), 'created_at' => $submission->created_at?->toISOString(), 'updated_at' => $submission->updated_at?->toISOString()];
    }
}
