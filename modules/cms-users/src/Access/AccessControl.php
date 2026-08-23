<?php

declare(strict_types=1);

namespace Liberu\Cms\Users\Access;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Liberu\Cms\Contracts\Access\AccessControlInterface;

/**
 * The authorization boundary, implemented over the framework gate.
 *
 * The gate is populated by the host's permission backend (Shield/Spatie), so
 * this class stays free of any concrete user, role, or permission class. Team
 * scoping is inherited automatically: the backend evaluates permissions within
 * the active tenant's context.
 */
final readonly class AccessControl implements AccessControlInterface
{
    public function __construct(
        private AuthFactory $auth,
        private Gate $gate,
    ) {}

    public function can(string $ability, mixed $arguments = null): bool
    {
        $user = $this->auth->guard()->user();

        return $user !== null && $this->canForUser($user, $ability, $arguments);
    }

    public function cannot(string $ability, mixed $arguments = null): bool
    {
        return ! $this->can($ability, $arguments);
    }

    public function canForUser(Authenticatable $user, string $ability, mixed $arguments = null): bool
    {
        return $this->gate->forUser($user)->allows($ability, $arguments ?? []);
    }

    public function authorize(string $ability, mixed $arguments = null): void
    {
        if ($this->cannot($ability, $arguments)) {
            throw new AuthorizationException('This action is unauthorized.');
        }
    }
}
