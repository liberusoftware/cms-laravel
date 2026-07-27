<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Module;

use Throwable;

/**
 * Marks failures caused by module dependency or lifecycle rule violations,
 * so callers can catch a stable contract type rather than a concrete class.
 *
 * @api This interface is part of the public extension API.
 */
interface ModuleDependencyExceptionInterface extends Throwable {}
