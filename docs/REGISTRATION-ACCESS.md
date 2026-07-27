# Registration & access model

How identities enter the system, what they get, and the knobs to lock it down.
This documents the deliberate access design (OWASP A04) alongside the auth
hardening from Phase 6 ticket 07.

## Self-registration is open by default

The `app` panel enables registration (`->registration()`), backed by Fortify
`Features::registration()`. Anyone can create an account. This is a deliberate
default for a general-purpose CMS; see **Locking it down** below to change it.

## What a new registrant gets

`App\Actions\Fortify\CreateNewUser` creates the user, then calls
`createPersonalTeam()` (`App\Traits\HasTeams`):

- A **personal team** the user owns and which becomes their current tenant.
- The team's **`super_admin` role, scoped to that team only** (Spatie teams).

This is least-privilege *per tenant*: a registrant is fully in control of their
own personal team and nothing else. Tenant scoping (`TenantScope` +
Filament tenancy) means they can never see or act on another tenant's data, and
their `super_admin` role carries no authority outside their own team. The
baseline CMS permissions are mapped onto roles by `CmsBaselineRolesSeeder`
(super_admin/admin = all, editor/author/viewer = progressively less).

## Auth hardening applied here

- **Passwords** must pass `Password::default()->uncompromised()` (HaveIBeenPwned
  breach check) on registration, reset, and change — `PasswordValidationRules`.
- **Two-factor is enforced for privileged roles.** A signed-in user holding a
  role in `config('two-factor.privileged_roles')` (super_admin, admin) is
  redirected to 2FA setup and cannot use the panel until enrolled
  (`EnsureTwoFactorForPrivilegedUsers`, panel auth middleware). Toggle with
  `TWO_FACTOR_ENFORCE`. Non-privileged users are unaffected.

## Throttles

- **Login**: 5/min per email+IP (`login` limiter, `FortifyServiceProvider`).
- **Two-factor challenge**: 5/min (`two-factor` limiter).
- **Password reset link**: throttled by Laravel's password broker (one email
  per throttle window per user).
- **Email verification**: `Features::emailVerification()` is currently disabled.
  Enable it to require verification; its route carries Laravel's `throttle:6,1`.

## Locking it down

To make registration invite-only or closed:

1. Remove `->registration()` from `App\Providers\Filament\AppPanelProvider`, and
   optionally comment `Features::registration()` in `config/fortify.php`.
2. Provision users by invitation or seeder, assigning the appropriate baseline
   role instead of the personal-team `super_admin`.

For a multi-tenant SaaS deployment, prefer invite-only registration so a
stranger cannot self-provision a tenant.
