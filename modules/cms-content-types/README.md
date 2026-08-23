# cms-content-types

Custom content types for Liberu CMS — define your own content shapes (portfolio
items, products, case studies, docs) with a JSON field schema, then create
validated, workflow-enabled entries against them.

Built on the **cms-content** foundation (workflow + versioning).

## Models

- **`ContentType`** — `key`, labels, and a `fields` JSON schema. `fieldDefinitions()`
  returns the schema as `FieldDefinition` value objects.
- **`ContentEntry`** — belongs to a type; `data` (JSON) holds the field values.
  `HasWorkflow` + `HasRevisions`, auto-slug from title. Reports its type's `key`
  as the content type in workflow events.

## Field schema

`FieldType`: `text`, `textarea`, `richtext`, `number`, `boolean`, `date`,
`select`, `media`. Each `FieldDefinition` has a name, label, type, `required`
flag, and (for `select`) options.

## Validation

`SchemaValidator::validate($type, $data)` enforces the schema — required fields
present, values roughly matching their type — and drops keys not in the schema,
throwing `InvalidContentData` on violation.

```php
$type = ContentType::create([
    'key' => 'portfolio', 'name' => 'Portfolio',
    'singular_label' => 'Item', 'plural_label' => 'Items',
    'fields' => [
        ['name' => 'summary', 'label' => 'Summary', 'type' => 'text', 'required' => true],
        ['name' => 'year', 'label' => 'Year', 'type' => 'number'],
    ],
]);

$data = app(SchemaValidator::class)->validate($type, ['summary' => 'A project', 'year' => 2026]);
$entry = ContentEntry::create(['content_type_id' => $type->id, 'title' => 'A project', 'data' => $data]);
$entry->publish();
```

## Repository

`ContentEntryRepositoryInterface`: `find`, `findBySlug`, `ofType(key)`, `published`.

## Events

- **Emits:** via the content foundation — `ContentStateChanged`, `ContentPublished`
  (carrying the custom type's key).
- **Listens:** none.
