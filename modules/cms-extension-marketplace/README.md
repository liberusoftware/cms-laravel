# CMS Extension Marketplace

The marketplace owns publisher, listing, category, version, signing, review,
licensing, trial, security, support, and distribution records. It is
provider-neutral and exposes its behavior through `ExtensionMarketplaceService`
and `ExtensionMarketplaceQuery`; API, Filament, and Livewire adapters are
optional matching packages.

All mutations validate their invariants, are tenant scoped, and keep release
signing and distribution metadata inside this module. Install the package with
Composer, enable the module, then run the module migrations. See the changelog
for compatibility and upgrade notes.
