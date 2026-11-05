<div class="cms-overview space-y-6">
    @php($setup = $this->setupDetails())
    <section class="cms-welcome" aria-labelledby="cms-welcome-title">
        <div class="cms-welcome-copy">
            <p class="cms-eyebrow">{{ $this->workspaceName() }}</p>
            <h2 id="cms-welcome-title">{{ \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin' ? __('Your CMS control center.') : __('A great website starts here.') }}</h2>
            <p>{{ \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin' ? __('Configure shared defaults and help each website team get started.') : __('Start with the essentials, build useful content, and grow at your own pace.') }}</p>
            @if ($setup)
                <div class="cms-welcome-action">
                    <x-filament::button :href="$setup['url']" tag="a" icon="heroicon-o-sparkles">
                        {{ $setup['complete'] ? __('Review setup') : ($setup['platform'] ? __('Set up your platform') : __('Set up your website')) }}
                    </x-filament::button>
                    <span class="cms-status">{{ $setup['complete'] ? __('Setup complete') : __('Three simple steps · progress saved') }}</span>
                </div>
            @else
                <p class="cms-status">{{ __('Use the tools below to manage content. Your team owner manages website setup.') }}</p>
            @endif
        </div>
        <div class="cms-welcome-symbol" aria-hidden="true">
            <x-filament::icon icon="heroicon-o-square-3-stack-3d" class="h-16 w-16" />
        </div>
    </section>
    @if ($links = $this->quickLinks())
        <section aria-labelledby="cms-quick-links-title">
            <h2 id="cms-quick-links-title" class="cms-section-title">{{ __('Your workspace') }}</h2>
            <div class="cms-shortcuts">
                @foreach ($links as $link)
                    <a href="{{ $link['url'] }}" class="cms-shortcut">
                        <x-filament::icon :icon="$link['icon']" class="h-6 w-6" />
                        <div>
                            <h3>{{ $link['title'] }}</h3>
                            <p>{{ $link['description'] }}</p>
                        </div>
                        <x-filament::icon icon="heroicon-o-arrow-up-right" class="h-4 w-4 cms-shortcut-arrow" />
                    </a>
                @endforeach
            </div>
        </section>
    @endif
    <x-filament::section :heading="__('Work at your own level')" :description="__('Start with pages and media. Use collections for structured content, then explore the tools available to your role as your needs grow.')" icon="heroicon-o-light-bulb">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Use the sidebar to find your tools. Switch teams from the team menu to keep each website’s content separate.') }}</p>
    </x-filament::section>
</div>
