# CMS Experimentation

The module owns experiments, variants, allocation, goals, guardrails,
analysis-policy metadata, winner promotion, and immutable promotion history.
Allocation is deterministic for a subject and never exposes a draft or
inactive experiment. API, Filament, and Livewire are optional adapters over the
core service/query boundary.
