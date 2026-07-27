<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Access;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * The single authorization boundary for the whole platform.
 *
 * Every module asks this contract "may the current user do X?" and never
 * touches the users table, a role model, or the permission backend directly.
 * The implementation lives in the Users module and is backed by the framework
 * gate (which Shield/Spatie populate); swapping the backend changes nothing for
 * consumers.
 *
 * @api This interface is part of the public extension API.
 */
interface AccessControlInterface
{
    /**
     * Whether the currently authenticated user may perform the ability.
     *
     * Returns false when no user is authenticated.
     *
     * @param  mixed  $arguments  Optional target (e.g. a model) for policy checks.
     */
    public function can(string $ability, mixed $arguments = null): bool;

    /**
     * The inverse of can(): true when the ability is denied.
     */
    public function cannot(string $ability, mixed $arguments = null): bool;

    /**
     * Whether a specific user may perform the ability.
     */
    public function canForUser(Authenticatable $user, string $ability, mixed $arguments = null): bool;

    /**
     * Authorize the current user or throw.
     *
     * @throws AuthorizationException
     */
    public function authorize(string $ability, mixed $arguments = null): void;
}
