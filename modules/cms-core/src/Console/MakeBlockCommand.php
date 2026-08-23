<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Console;

/**
 * Scaffolds a content block type inside an existing module: a BlockTypeInterface
 * implementation the renderer can resolve by key. The generated class is
 * complete; register it with the one-liner printed on success.
 */
final class MakeBlockCommand extends AbstractMakeArtifactCommand
{
    #[\Override]
    protected $signature = 'cms:make-block
        {module : The module key the block belongs to, e.g. hello}
        {name : The block name, e.g. Quote}';

    #[\Override]
    protected $description = 'Scaffold a CMS block type inside a module';

    public function handle(): int
    {
        $target = $this->resolveTarget();

        if ($target === null) {
            return self::FAILURE;
        }

        $path = "{$target['base']}/src/Blocks/{$target['studly']}Block.php";

        $written = $this->writeStub($path, $this->stub(), [
            '{{namespace}}' => "Liberu\\Cms\\{$target['moduleStudly']}\\Blocks",
            '{{studly}}' => $target['studly'],
            '{{key}}' => $target['key'],
        ]);

        if (! $written) {
            return self::FAILURE;
        }

        $this->components->info("Block [{$target['studly']}Block] created in cms-{$target['moduleKey']}.");
        $this->components->bulletList([
            'Register it in your module\'s bootModule():',
            'if ($this->app->bound(BlockTypeRegistryInterface::class)) {',
            "    \$this->app->make(BlockTypeRegistryInterface::class)->register(new Blocks\\{$target['studly']}Block);",
            '}',
            'Import: use Liberu\\Cms\\Contracts\\Block\\BlockTypeRegistryInterface;',
        ]);

        return self::SUCCESS;
    }

    private function stub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        namespace {{namespace}};

        use Liberu\Cms\Contracts\Block\BlockTypeInterface;

        /**
         * The {{studly}} content block. The renderer resolves it by its key,
         * "{{key}}"; register it through BlockTypeRegistryInterface.
         */
        final class {{studly}}Block implements BlockTypeInterface
        {
            public function key(): string
            {
                return '{{key}}';
            }

            /**
             * @param  array<array-key, mixed>  $data
             */
            public function render(array $data, string $childrenHtml = ''): string
            {
                $text = is_string($data['text'] ?? null) ? $data['text'] : '';

                return '<div class="cms-block-{{key}}">'.e($text).$childrenHtml.'</div>';
            }
        }

        STUB;
    }
}
