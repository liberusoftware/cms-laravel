<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Forms\Database\Factories\FormSubmissionFactory;

/**
 * One accepted submission of a form: the validated field values plus request
 * metadata (ip, user agent).
 *
 * @property int $id
 * @property int $form_id
 * @property array<string, mixed>|null $data
 * @property array<string, mixed>|null $meta
 * @property int|null $team_id
 */
final class FormSubmission extends Model
{
    /** @use HasFactory<FormSubmissionFactory> */
    use HasFactory;

    use HasTenant;

    #[\Override]
    protected $table = 'cms_form_submissions';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['form_id', 'data', 'meta', 'team_id'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['data' => 'array', 'meta' => 'array'];
    }

    /**
     * @return BelongsTo<Form, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    protected static function newFactory(): FormSubmissionFactory
    {
        return FormSubmissionFactory::new();
    }
}
