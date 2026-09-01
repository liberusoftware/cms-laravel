# CMS Forums Integration

Provider-neutral community integration boundary. Providers implement the
`ForumProvider` contract for recent discussions, moderation links, and SSO
context; this module stores only tenant-scoped external references and never
imports a provider SDK.
