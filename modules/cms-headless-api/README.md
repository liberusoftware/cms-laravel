# CMS Headless API

Provider-neutral delivery boundary for REST or GraphQL-style transports. The
`DeliveryQuery` value object validates sparse fields, includes, filters,
pagination, locale, preview, and version options. Persisted queries are
tenant-scoped and keyed by a stable SHA-256 hash; transport adapters can use
the result without coupling the domain module to HTTP or GraphQL.
