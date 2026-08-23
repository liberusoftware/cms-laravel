<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Fields;

use Closure;

/**
 * One content-type field kind: how a value of this kind is edited, and how it is
 * validated. Built-in kinds (text, number, …) and third-party kinds (color, geo,
 * …) are registered the same way, so there is no closed set.
 *
 * Deliberately does not import Filament: the edit component's type is threaded
 * through the {@see TComponent} generic so this contract stays framework-agnostic
 * while the consuming Filament resource keeps full static typing.
 *
 * @template-covariant TComponent of object
 *
 * @api This class is part of the public extension API.
 */
final readonly class FieldTypeDefinition
{
    /**
     * @param  string  $key  Stable kind key stored in a field schema, e.g. "color".
     * @param  string  $label  Human label shown in the schema editor.
     * @param  Closure(string, list<string>): TComponent  $component  Builds the edit component, given the field's state path and its options.
     * @param  Closure(mixed): bool  $matches  Whether a raw value roughly matches this kind (schema validation).
     */
    public function __construct(
        public string $key,
        public string $label,
        public Closure $component,
        public Closure $matches,
    ) {}
}
