# CMS Taxonomy

The Taxonomy module owns reusable vocabularies, hierarchical terms, synonyms,
translations, ordered assignments, exclusive vocabularies, and safe term
merging. It is tenant-aware and exposes the domain through `TaxonomyService`.

Existing Posts `Category` and `Tag` models remain supported; this module is an
independent vocabulary boundary for new integrations.
