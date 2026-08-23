# CMS Forms

Public form definitions and spam-protected submissions. Each accepted submission
is stored and announced on the event bus as `FormSubmitted`, so a notification
or automation module can react without this module knowing about it.

## Submitting

```
POST /forms/{slug}
```

- Public (no authentication) — a visitor's browser posts directly. Rate-limited
  per IP (`config('cms-forms.rate_limit')`).
- The payload is validated against the form's field schema; a required field
  missing or a value failing its type rule returns `422`.
- A filled honeypot field (`config('cms-forms.honeypot')`, default `_hp`) marks
  the request as a bot: it is accepted with `201` but not stored.
- On success the submission is stored, stamped with the form's tenant, and a
  `Liberu\Cms\Contracts\Events\Form\FormSubmitted` event is dispatched.

## Defining a form

Manage forms in the admin panel (Filament) under **CMS → Forms**, where the field
schema is edited as a repeatable list and submissions are viewed under **CMS →
Form submissions**. Forms can also be created programmatically:

```php
Form::create([
    'name' => 'Contact',
    'fields' => [
        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
        ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true],
    ],
]);
```

Field `type` is one of: `text`, `email`, `textarea`, `number`, `checkbox`.

## Reacting to submissions

```php
$events->listen(FormSubmitted::class, function (FormSubmitted $event): void {
    // notify, forward to a CRM, ...
});
```

## Config

Publish with `php artisan vendor:publish --tag=cms-forms-config`: `honeypot`,
`rate_limit`, `success_message`.
