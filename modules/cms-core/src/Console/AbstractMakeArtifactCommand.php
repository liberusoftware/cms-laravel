<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Shared plumbing for the within-module artifact generators (block, hook, field
 * type). Each resolves an existing target module, then writes typed stubs into
 * it; unlike cms:make-module, these add a feature to a module that already
 * exists rather than scaffolding a whole package.
 */
abstract class AbstractMakeArtifactCommand extends Command
{
    public function __construct(protected readonly Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Resolve the target module and the artifact name into the parts the stubs
     * need, or null (with an error printed) when the module does not exist.
     *
     * @return array{base: string, moduleKey: string, moduleStudly: string, studly: string, key: string}|null
     */
    protected function resolveTarget(): ?array
    {
        $moduleKey = Str::kebab(Str::studly($this->argument('module')));
        $base = base_path("modules/cms-{$moduleKey}");

        if (! $this->files->isDirectory($base)) {
            $this->components->error("Module [cms-{$moduleKey}] does not exist. Run cms:make-module first.");

            return null;
        }

        $studly = Str::studly($this->argument('name'));

        return [
            'base' => $base,
            'moduleKey' => $moduleKey,
            'moduleStudly' => Str::studly($moduleKey),
            'studly' => $studly,
            'key' => Str::kebab($studly),
        ];
    }

    /**
     * Write a stub, refusing to overwrite an existing file.
     *
     * @param  array<string, string>  $replacements
     */
    protected function writeStub(string $path, string $stub, array $replacements): bool
    {
        if ($this->files->exists($path)) {
            $this->components->error(basename($path).' already exists at '.$path.'.');

            return false;
        }

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, strtr($stub, $replacements));

        return true;
    }
}
