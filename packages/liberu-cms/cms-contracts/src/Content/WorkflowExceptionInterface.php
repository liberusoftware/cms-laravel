<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Content;

use Throwable;

/**
 * Marks an illegal editorial state transition, so callers can catch a stable
 * contract type rather than a concrete class.
 *
 * @api This interface is part of the public extension API.
 */
interface WorkflowExceptionInterface extends Throwable {}
