<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Content\Revisions\HasRevisions;
use Liberu\Cms\Content\Workflow\HasWorkflow;

/**
 * A minimal content model used to exercise the cms-content foundation traits in
 * isolation, ahead of the real Page/Post modules.
 *
 * @property string|null $title
 */
class WorkflowContent extends Model
{
    use HasRevisions;
    use HasWorkflow;

    #[\Override]
    protected $table = 'workflow_contents';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['title', 'status', 'published_at'];
}
