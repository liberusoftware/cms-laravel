<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $message
 */
final class Greeting extends Model
{
    #[\Override]
    protected $table = 'hello_greetings';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['name', 'message'];
}
