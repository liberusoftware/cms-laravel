<?php

declare(strict_types=1);

use Liberu\Cms\Blocks\Types\CodeBlock;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;
use Liberu\Cms\Contracts\Events\Media\MediaUploaded;
use Liberu\Cms\Contracts\Events\Theme\ThemeActivated;
use Liberu\Cms\Contracts\Hooks\Filters\AdminFormSchemaFilter;
use Liberu\Cms\Contracts\Hooks\Filters\ApiResourceFilter;
use Liberu\Cms\Contracts\Hooks\Filters\BlockRenderFilter;
use Liberu\Cms\Contracts\Hooks\Filters\ContentQueryFilter;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Core\Module\ArrayModuleStateRepository;
use Liberu\Cms\Core\Tenant\NullTenantResolver;
use Liberu\Cms\Hello\Events\HelloGreeted;
use Liberu\Cms\Observability\Health\Checks\CacheHealthCheck;
use Liberu\Cms\Observability\Health\Checks\DatabaseHealthCheck;
use Liberu\Cms\Observability\Health\Checks\QueueHealthCheck;
use Liberu\Cms\Search\Health\SearchHealthCheck;
use Liberu\Cms\Seo\Http\Controllers\RobotsController;
use Liberu\Cms\Themes\AbstractTheme;
use Liberu\Cms\Widgets\WidgetRegistry;

it('covers the public event value objects and their names', function (): void {
    $state = new ContentStateChanged('page', 7, WorkflowState::Draft, WorkflowState::Published);
    $media = new MediaUploaded(Mockery::mock(MediaItemInterface::class));
    $theme = new ThemeActivated('child', 'default');
    $greeting = new HelloGreeted('Ada', 'Hello, Ada!');

    expect($state->name())->toBe('content.state_changed')
        ->and($media->name())->toBe('media.uploaded')
        ->and($theme->name())->toBe('theme.activated')
        ->and($greeting->name())->toBe('hello.greeted');
});

it('provides safe defaults for the null tenant and array state adapters', function (): void {
    $tenant = new NullTenantResolver;
    $state = new ArrayModuleStateRepository(['pages' => false]);

    expect($tenant->tenantModel())->toBeNull()
        ->and($tenant->currentTenantId())->toBeNull()
        ->and($state->isEnabled('pages'))->toBeFalse()
        ->and($state->isEnabled('posts'))->toBeTrue();

    $state->setEnabled('posts', false);
    $state->forget('pages');

    expect($state->isEnabled('pages'))->toBeTrue()
        ->and($state->isEnabled('posts'))->toBeFalse();
});

it('renders code blocks with escaped language and source', function (): void {
    $html = (new CodeBlock)->render(['language' => 'php"', 'code' => '<script>alert(1)</script>']);

    expect($html)->toContain('language-php&quot;')
        ->and($html)->toContain('&lt;script&gt;alert(1)&lt;/script&gt;');
});

it('uses a null parent for base themes', function (): void {
    $theme = new class extends AbstractTheme
    {
        public function key(): string
        {
            return 'test';
        }

        public function name(): string
        {
            return 'Test';
        }

        public function viewsPath(): string
        {
            return '/tmp';
        }
    };

    expect($theme->parent())->toBeNull();
});

it('reports successful and failing readiness probes through their contracts', function (): void {
    $cache = Mockery::mock('Illuminate\\Contracts\\Cache\\Factory');
    $cache->shouldReceive('store')->twice()->andReturnSelf();
    $cache->shouldReceive('get')->once()->andReturn(null);
    $cache->shouldReceive('get')->once()->andThrow(new RuntimeException('cache down'));

    $database = Mockery::mock('Illuminate\\Database\\ConnectionResolverInterface');
    $database->shouldReceive('connection')->twice()->andReturnSelf();
    $database->shouldReceive('select')->once()->andReturn([]);
    $database->shouldReceive('select')->once()->andThrow(new RuntimeException('db down'));

    $queue = Mockery::mock('Illuminate\\Contracts\\Queue\\Factory');
    $queue->shouldReceive('connection')->twice()->andReturnSelf();
    $queue->shouldReceive('size')->once()->andReturn(0);
    $queue->shouldReceive('size')->once()->andThrow(new RuntimeException('queue down'));

    $search = Mockery::mock('Liberu\\Cms\\Contracts\\Search\\SearchIndexInterface');
    $search->shouldReceive('isReady')->twice()->andReturn(true, false);

    expect((new CacheHealthCheck($cache, false))->check())->toBeTrue()
        ->and((new CacheHealthCheck($cache, false))->isCritical())->toBeFalse()
        ->and((new CacheHealthCheck($cache, false))->check())->toBeFalse()
        ->and((new DatabaseHealthCheck($database, true))->check())->toBeTrue()
        ->and((new DatabaseHealthCheck($database, true))->isCritical())->toBeTrue()
        ->and((new DatabaseHealthCheck($database, true))->check())->toBeFalse()
        ->and((new QueueHealthCheck($queue, false))->check())->toBeTrue()
        ->and((new QueueHealthCheck($queue, false))->isCritical())->toBeFalse()
        ->and((new QueueHealthCheck($queue, false))->check())->toBeFalse()
        ->and((new SearchHealthCheck($search, false))->check())->toBeTrue()
        ->and((new SearchHealthCheck($search, false))->isCritical())->toBeFalse()
        ->and((new SearchHealthCheck($search, false))->check())->toBeFalse();
});

it('exposes stable names for the public hook filter value objects', function (): void {
    expect((new AdminFormSchemaFilter([], 'pages'))->name())->toBe('pages.admin.form')
        ->and((new ApiResourceFilter([], null))->name())->toBe('api.resource')
        ->and((new BlockRenderFilter('', 'text', []))->name())->toBe('blocks.render')
        ->and((new ContentQueryFilter('pages.published', Mockery::mock('Illuminate\\Database\\Eloquent\\Builder')))->name())
        ->toBe('pages.published');
});

it('keeps empty widget registries safe', function (): void {
    expect((new WidgetRegistry)->all())->toBe([]);
});

it('skips malformed robots groups and non-string disallow values', function (): void {
    config()->set('cms-seo.robots.groups', [
        'malformed',
        ['user_agent' => 123, 'disallow' => [42, '/admin']],
    ]);

    $response = (new RobotsController)();

    expect($response->getContent())->toContain("User-agent: *\nDisallow: /admin")
        ->and($response->getContent())->toContain('Sitemap: ');
});
