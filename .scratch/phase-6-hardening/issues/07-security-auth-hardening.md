# 07 — Security: authentication hardening

**What to build:** Tighten identity and access defaults — breached-password checks, enforced 2FA for privileged roles, and a defined self-registration → team/role model — closing OWASP A02/A07 recommendations and A04 (insecure design: undefined registration access).

**Blocked by:** 04 (policies/roles) — the 2FA enforcement and default-role assignment reference the role set defined there.

**Status:** ready-for-agent

- [ ] Add `->uncompromised()` to the password validation rules (HaveIBeenPwned check) for registration + reset.
- [ ] **Enforce 2FA for privileged roles**: users holding admin/privileged permissions cannot reach the panel without 2FA enrolled (leverage the installed `stephenjude/filament-two-factor-authentication`). Non-privileged users unaffected.
- [ ] Define the **self-registration → team/role** flow (OWASP A04): what team a new registrant gets (existing `createPersonalTeam`) and what default role/permissions; for multi-tenant mode, gate registration behind invite or document the deliberate open-registration decision.
- [ ] Confirm password-reset + email-verification throttles are active.
- [ ] Behavior guarantees: a compromised password is rejected; a privileged user without 2FA is blocked from privileged actions until enrolled; a fresh registrant lands with exactly the documented team + role (least privilege).
- [ ] DoD: auth defaults hardened + tested; registration access model documented; Pint + PHPStan max + full suite green.
