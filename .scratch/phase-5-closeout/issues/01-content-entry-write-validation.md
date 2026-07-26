# 01 — ContentEntry write schema validation

**What to build:** When an authenticated writer POSTs/PUTs a ContentEntry through the Delivery API, its `data` payload is validated against the selected ContentType's JSON `FieldDefinition` schema before persistence, returning `422` with per-field messages on violation. Closes the "writes trust the payload" gap left open in Phase 5 · Increment 4.

**Blocked by:** None — extends existing `cms-api-writes` + `cms-content-types`.

**Status:** ready-for-agent

- [ ] Reuse the existing `SchemaValidator` (cms-content-types) to turn a ContentType's `FieldDefinition[]` into validation rules; do not duplicate the field→rule mapping.
- [ ] `StoreContentEntryRequest` / `UpdateContentEntryRequest` validate `data.*` against the resolved ContentType schema (required, type, and any per-field constraints the schema expresses).
- [ ] Unknown keys not in the schema are rejected (or stripped — pick one and test it); type mismatches (e.g. string for a number field) → `422`.
- [ ] Update path resolves the ContentType from the persisted entry (client cannot switch type to bypass rules).
- [ ] Behavior guarantees (feature tests, HTTP seam): valid payload → `201`/`200`; missing required field → `422`; wrong-typed field → `422`; the `422` body names the offending field(s).
- [ ] Pint + PHPStan max + full Pest suite green.
