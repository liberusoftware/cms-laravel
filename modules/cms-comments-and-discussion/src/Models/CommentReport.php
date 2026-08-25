<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussion\Models;

use Illuminate\Database\Eloquent\Model;

final class CommentReport extends Model
{
    #[\Override]
    protected $table = 'cms_comment_reports';

    #[\Override]
    protected $fillable = ['comment_id', 'reporter_id', 'reason', 'status'];
}
