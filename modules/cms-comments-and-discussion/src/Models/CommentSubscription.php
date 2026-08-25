<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussion\Models;

use Illuminate\Database\Eloquent\Model;

final class CommentSubscription extends Model
{
    #[\Override]
    protected $table = 'cms_comment_subscriptions';

    #[\Override]
    protected $fillable = ['comment_id', 'subscriber_id'];
}
