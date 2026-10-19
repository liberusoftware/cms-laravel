<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Contracts\HasApiTokens as HasApiTokensContract;
use Laravel\Sanctum\HasApiTokens;

class Team extends Model implements AuthenticatableContract, HasApiTokensContract
{
    use Authenticatable;
    use HasApiTokens;
    use HasFactory;

    #[\Override]
    protected $fillable = [
        'user_id',
        'name',
        'personal_team',
        'settings',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'settings' => 'encrypted:array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function allUsers(): Collection
    {
        return $this->users->merge([$this->owner]);
    }

    public function hasUser(User $user): bool
    {
        return $this->users->contains($user) || $user->ownsTeam($this);
    }

    public function hasUserWithEmail(string $email): bool
    {
        return $this->allUsers()->contains(fn ($user): bool => $user->email === $email);
    }

    public function removeUser(User $user): void
    {
        if ($user->current_team_id === $this->id) {
            $user->forceFill(['current_team_id' => null])->save();
        }

        $this->users()->detach($user);
    }

    #[\Override]
    protected static function booted(): void
    {
        static::created(function (Team $team): void {
            foreach (['super_admin' => [], 'editor' => [], 'viewer' => []] as $roleName => $permissions) {
                $role = Role::create([
                    'name' => $roleName,
                    'team_id' => $team->id,
                    'guard_name' => 'web',
                ]);

                $role->syncPermissions($permissions);
            }

            if (auth()->check()) {
                $user = auth()->user();
                $user->teams()->syncWithoutDetaching([$team->id]);

                $oldTeam = getPermissionsTeamId();
                setPermissionsTeamId($team->id);

                $superAdminRole = Role::where('name', 'super_admin')
                    ->where('team_id', $team->id)
                    ->first();

                $user->assignRole($superAdminRole);

                setPermissionsTeamId($oldTeam);
            }
        });
    }
}
