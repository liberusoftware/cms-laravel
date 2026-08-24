# CMS API modules

The API adapter is `liberusoftware/cms-api`. Content, page, post, menu, form,
search, SEO, preview, and token endpoints are registered by the owning module
through the public contracts and the API registry. Controllers validate input,
resolve authenticated tenant/team context, and delegate domain behavior to
core services.

Every future API scope must have a matching core module, versioned OpenAPI
contract, authorization and tenancy tests, and documented error and rate-limit
behavior. API adapters must not query another module's private tables.
