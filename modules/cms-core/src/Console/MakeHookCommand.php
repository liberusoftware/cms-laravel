<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Console;

/**
 * Scaffolds both sides of a hook: a Filter marker with a mutable payload, and an
 * example listener that transforms it. The Filter idiom is new, so seeing the
 * emitting value object and a consuming callback together is the fastest way to
 * learn it.
 */
final class MakeHookCommand extends AbstractMakeArtifactCommand
{
    #[\Override]
    protected $signature = 'cms:make-hook
        {module : The module key the hook belongs to, e.g. hello}
        {name : The hook name, e.g. Greeting}';

    #[\Override]
    protected $description = 'Scaffold a CMS hook (Filter + listener) inside a module';

    public function handle(): int
    {
        $target = $this->resolveTarget();

        if ($target === null) {
            return self::FAILURE;
        }

        $replacements = [
            '{{namespace}}' => "Liberu\\Cms\\{$target['moduleStudly']}\\Hooks",
            '{{studly}}' => $target['studly'],
            '{{name}}' => "{$target['moduleKey']}.{$target['key']}",
        ];

        $filterPath = "{$target['base']}/src/Hooks/{$target['studly']}Filter.php";
        $listenerPath = "{$target['base']}/src/Hooks/{$target['studly']}Listener.php";

        if (! $this->writeStub($filterPath, $this->filterStub(), $replacements)) {
            return self::FAILURE;
        }

        if (! $this->writeStub($listenerPath, $this->listenerStub(), $replacements)) {
            return self::FAILURE;
        }

        $this->components->info("Hook [{$target['studly']}Filter] + listener created in cms-{$target['moduleKey']}.");
        $this->components->bulletList([
            'Register the listener in your module\'s bootModule():',
            '$this->app->make(HookBusInterface::class)->listen(',
            "    Hooks\\{$target['studly']}Filter::class,",
            "    Hooks\\{$target['studly']}Listener::class,",
            ');',
            'Import: use Liberu\\Cms\\Contracts\\Hooks\\HookBusInterface;',
            "Apply the filter at your emit site: \$hooks->apply(new {$target['studly']}Filter(\$value))->value;",
        ]);

        return self::SUCCESS;
    }

    private function filterStub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        namespace {{namespace}};

        use Liberu\Cms\Contracts\Hooks\Filter;

        /**
         * The {{studly}} filter point. Callbacks registered on the HookBus receive
         * this instance in priority order and mutate {@see $value} in place; the
         * emitter then reads the transformed value back.
         */
        final class {{studly}}Filter implements Filter
        {
            public function __construct(public string $value) {}

            public function name(): string
            {
                return '{{name}}';
            }
        }

        STUB;
    }

    private function listenerStub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        namespace {{namespace}};

        /**
         * Example consumer of {@see {{studly}}Filter}. Registered on the HookBus, it
         * receives the filter and transforms its payload in place.
         */
        final class {{studly}}Listener
        {
            public function __invoke({{studly}}Filter $filter): void
            {
                $filter->value = trim($filter->value);
            }
        }

        STUB;
    }
}
