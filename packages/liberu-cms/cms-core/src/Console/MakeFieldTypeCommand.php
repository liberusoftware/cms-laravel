<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Console;

/**
 * Scaffolds a custom content-type field kind: a class that registers a
 * FieldTypeDefinition (edit component + validation predicate) into the field-type
 * registry, so the kind appears in the schema editor and validates like a
 * built-in. Requires filament/filament for the component factory.
 */
final class MakeFieldTypeCommand extends AbstractMakeArtifactCommand
{
    #[\Override]
    protected $signature = 'cms:make-field-type
        {module : The module key the field type belongs to, e.g. hello}
        {name : The field type name, e.g. Color}';

    #[\Override]
    protected $description = 'Scaffold a custom content-type field kind inside a module';

    public function handle(): int
    {
        $target = $this->resolveTarget();

        if ($target === null) {
            return self::FAILURE;
        }

        $path = "{$target['base']}/src/Fields/{$target['studly']}FieldType.php";

        $written = $this->writeStub($path, $this->stub(), [
            '{{namespace}}' => "Liberu\\Cms\\{$target['moduleStudly']}\\Fields",
            '{{studly}}' => $target['studly'],
            '{{key}}' => $target['key'],
        ]);

        if (! $written) {
            return self::FAILURE;
        }

        $this->components->info("Field type [{$target['studly']}FieldType] created in cms-{$target['moduleKey']}.");
        $this->components->bulletList([
            'Register it in your module\'s registerModule():',
            'if ($this->app->bound(FieldTypeRegistryInterface::class)) {',
            "    Fields\\{$target['studly']}FieldType::registerInto(\$this->app->make(FieldTypeRegistryInterface::class));",
            '}',
            'Import: use Liberu\\Cms\\Contracts\\Fields\\FieldTypeRegistryInterface;',
        ]);

        return self::SUCCESS;
    }

    private function stub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        namespace {{namespace}};

        use Filament\Forms\Components\TextInput;
        use Liberu\Cms\Contracts\Fields\FieldTypeDefinition;
        use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

        /**
         * Registers the "{{key}}" content-type field kind: how a value of this kind
         * is edited in the admin form, and how it is validated. Adjust the component
         * factory and the match predicate to suit the kind.
         */
        final class {{studly}}FieldType
        {
            public static function registerInto(FieldTypeRegistryInterface $registry): void
            {
                $registry->register(new FieldTypeDefinition(
                    '{{key}}',
                    '{{studly}}',
                    static fn (string $path, array $options): TextInput => TextInput::make($path),
                    static fn (mixed $value): bool => is_string($value),
                ));
            }
        }

        STUB;
    }
}
