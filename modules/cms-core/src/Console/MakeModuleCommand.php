<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Scaffolds a new, §5-conforming CMS module package under
 * modules/cms-{key} and wires it into the root composer.json.
 *
 * The generated module boots, enables, and disables out of the box, so it can
 * pass CI before a single feature is written.
 */
final class MakeModuleCommand extends Command
{
    #[\Override]
    protected $signature = 'cms:make-module
        {name : The module name, e.g. Portfolio}
        {--foundational : Mark the module as non-removable}';

    #[\Override]
    protected $description = 'Generate a new Liberu CMS module package';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $studly = Str::studly($this->argument('name'));
        $key = Str::kebab($studly);
        $foundational = (bool) $this->option('foundational');

        $base = base_path("modules/cms-{$key}");

        if ($this->files->isDirectory($base)) {
            $this->components->error("Module [cms-{$key}] already exists at {$base}.");

            return self::FAILURE;
        }

        foreach ([$base, "{$base}/src", "{$base}/config", "{$base}/routes", "{$base}/database/migrations", "{$base}/tests"] as $dir) {
            $this->files->ensureDirectoryExists($dir);
        }

        $replacements = [
            '{{studly}}' => $studly,
            '{{key}}' => $key,
            '{{namespace}}' => "Liberu\\Cms\\{$studly}",
            '{{foundational}}' => $foundational ? 'true' : 'false',
        ];

        $this->writeComposerJson($base, $studly, $key);
        $this->write("{$base}/README.md", $this->readmeStub(), $replacements);
        $this->write("{$base}/config/{$key}.php", $this->configStub(), $replacements);
        $this->write("{$base}/routes/api.php", $this->routesStub(), $replacements);
        $this->write("{$base}/src/{$studly}Module.php", $this->moduleStub(), $replacements);
        $this->write("{$base}/src/{$studly}ServiceProvider.php", $this->providerStub(), $replacements);
        $this->write("{$base}/tests/{$studly}ModuleTest.php", $this->testStub(), $replacements);
        $this->files->put("{$base}/database/migrations/.gitkeep", '');

        $this->addToRootComposer($key);

        $this->components->info("Module [cms-{$key}] created at modules/cms-{$key}.");
        $this->components->bulletList([
            'Run: composer update liberusoftware/cms-'.$key,
            'Then: php artisan migrate',
        ]);

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function write(string $path, string $stub, array $replacements): void
    {
        $this->files->put($path, strtr($stub, $replacements));
    }

    private function addToRootComposer(string $key): void
    {
        $path = base_path('composer.json');

        $decoded = json_decode($this->files->get($path), true);
        $composer = is_array($decoded) ? $decoded : [];

        /** @var array<string, string> $require */
        $require = is_array($composer['require'] ?? null) ? $composer['require'] : [];
        $require["liberusoftware/cms-{$key}"] = '^0.1';
        ksort($require);
        $composer['require'] = $require;

        $this->files->put(
            $path,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n",
        );
    }

    private function writeComposerJson(string $base, string $studly, string $key): void
    {
        $namespace = "Liberu\\Cms\\{$studly}\\";

        $composer = [
            'name' => "liberusoftware/cms-{$key}",
            'description' => "The {$studly} module for Liberu CMS.",
            'type' => 'liberu-module',
            'license' => 'MIT',
            'version' => '0.1.0',
            'require' => [
                'php' => '^8.5',
                'liberusoftware/cms-contracts' => '^0.1',
                'liberusoftware/cms-core' => '^0.1',
            ],
            'autoload' => ['psr-4' => [$namespace => 'src/']],
            'autoload-dev' => ['psr-4' => ["{$namespace}Tests\\" => 'tests/']],
            'extra' => [
                'laravel' => ['providers' => ["{$namespace}{$studly}ServiceProvider"]],
                'liberu' => ['name' => "cms-{$key}"],
            ],
            'minimum-stability' => 'dev',
            'prefer-stable' => true,
        ];

        $this->files->put(
            "{$base}/composer.json",
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n",
        );
    }

    private function moduleStub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        namespace {{namespace}};

        use Liberu\Cms\Core\Module\AbstractModule;

        final class {{studly}}Module extends AbstractModule
        {
            public function key(): string
            {
                return '{{key}}';
            }

            public function name(): string
            {
                return '{{studly}}';
            }

            public function version(): string
            {
                return '0.1.0';
            }

            public function isFoundational(): bool
            {
                return {{foundational}};
            }
        }

        STUB;
    }

    private function providerStub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        namespace {{namespace}};

        use Liberu\Cms\Contracts\Module\ModuleInterface;
        use Liberu\Cms\Core\Module\ModuleServiceProvider;

        final class {{studly}}ServiceProvider extends ModuleServiceProvider
        {
            public function module(): ModuleInterface
            {
                return new {{studly}}Module;
            }

            protected function registerModule(): void
            {
                $this->mergeModuleConfig(__DIR__.'/../config/{{key}}.php', '{{key}}');
            }

            protected function bootModule(): void
            {
                $this->loadModuleMigrations(__DIR__.'/../database/migrations');
                $this->loadModuleRoutesFrom(__DIR__.'/../routes/api.php');
            }
        }

        STUB;
    }

    private function configStub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        return [
            //
        ];

        STUB;
    }

    private function routesStub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        use Illuminate\Support\Facades\Route;

        Route::prefix('api/v1/{{key}}')->group(function (): void {
            //
        });

        STUB;
    }

    private function testStub(): string
    {
        return <<<'STUB'
        <?php

        declare(strict_types=1);

        use {{namespace}}\{{studly}}Module;

        it('describes the {{key}} module', function (): void {
            $module = new {{studly}}Module;

            expect($module->key())->toBe('{{key}}')
                ->and($module->name())->toBe('{{studly}}')
                ->and($module->version())->not->toBeEmpty()
                ->and($module->isFoundational())->toBeFalse();
        });

        STUB;
    }

    private function readmeStub(): string
    {
        return <<<'STUB'
        # cms-{{key}}

        The {{studly}} module for Liberu CMS.

        ## Install

        ```bash
        composer update liberusoftware/cms-{{key}}
        php artisan migrate
        ```

        ## Config keys

        Published from `config/{{key}}.php` (merged under the `{{key}}` key).

        ## Events

        - **Emits:** _none yet_
        - **Listens:** _none yet_

        ## Public contracts

        _none yet_

        ## Extension points

        _none yet_
        STUB;
    }
}
