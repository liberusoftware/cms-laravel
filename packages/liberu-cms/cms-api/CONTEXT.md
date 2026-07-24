# CMS API

The headless HTTP delivery layer over the CMS content modules: a versioned, Sanctum-authenticated JSON API that serves published content to decoupled frontends. Infrastructure lives here; each content module registers its own resources.

## Language

**Delivery API**:
The read-only, versioned (`/api/v1`) HTTP surface that serves published content as JSON to external consumers.
_Avoid_: content API, public API, REST layer

**Delivery token**:
A Sanctum token whose tokenable is a `Team`, granting a consumer read access to that one tenant's published content. Authenticates and identifies the tenant in a single credential.
_Avoid_: API key, access token, user token

**Tenant context**:
The request-scoped holder that names the current tenant during an API request, set from the Delivery token. The tenancy resolver reads it before falling back to the Filament panel's tenant.
_Avoid_: current team, active tenant, scope

**API resource registry**:
The contract (`ApiResourceRegistryInterface`) through which a content module contributes its API endpoints and Eloquent Resources to the Delivery API, without the API package importing the module.
_Avoid_: resource map, route registry
