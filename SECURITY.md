# Security Policy

## Reporting a Vulnerability

We take security seriously. If you discover a security vulnerability in the Liberu CMS platform, please report it responsibly.

**Do not open a public issue for security vulnerabilities.**

Instead, report vulnerabilities privately through GitHub's security advisory feature:

1. Navigate to the repository's **Security** tab.
2. Click **"Report a vulnerability"**.
3. Provide a detailed description of the vulnerability, including steps to reproduce and potential impact.

You will receive an acknowledgment of your report within a reasonable timeframe.

## What to Expect

- This project is **volunteer-maintained**; there is no guaranteed response time or SLA.
- We will investigate all legitimate reports and do our best to address confirmed vulnerabilities promptly.
- You will be credited in the security advisory unless you prefer to remain anonymous.

## Scope

The following are in scope for security reports:

- `app/` — Application code (models, controllers, services, etc.)
- `routes/` — Route definitions and middleware
- `database/` — Migrations, seeders, and factories
- `config/` — Configuration files
- `resources/` — Views and frontend assets
- API endpoints and authentication mechanisms
- Filament admin panel and Livewire components

## Out of Scope

The following are **not** in scope:

- Third-party dependencies (report vulnerabilities to the respective upstream projects)
- Issues requiring `APP_DEBUG=true` in production
- Social engineering or physical attacks
- Denial of service attacks against infrastructure
- Vulnerabilities in outdated versions that are no longer supported

## Supported Versions

| Version | Supported |
|---------|-----------|
| Latest  | Yes       |
| Previous major | No  |

We recommend always running the latest version of the platform to ensure you have the most up-to-date security fixes.
