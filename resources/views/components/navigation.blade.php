<header class="flex flex-wrap md:justify-start md:flex-nowrap z-50 w-full bg-blue-600">
    <nav x-data="{ open: false }" class="relative max-w-264 w-full md:flex md:items-center md:justify-between md:gap-3 mx-auto px-4 sm:px-6 lg:px-8 py-2">
      <div class="flex items-center justify-between">
        <a class="flex-none font-semibold text-xl text-white focus:outline-none focus:opacity-80" href="{{ url('/') }}" aria-label="Brand"> {{ config('app.name') }} </a>

        <div class="md:hidden">
          <button type="button" x-on:click="open = ! open" x-bind:aria-expanded="open ? 'true' : 'false'" class="relative size-9 flex justify-center items-center text-sm font-semibold rounded-lg border border-white/50 text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 disabled:opacity-50 disabled:pointer-events-none" id="primary-navigation-toggle" aria-expanded="false" aria-controls="primary-navigation" aria-label="Toggle navigation">
            <svg x-show="! open" class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
            <svg x-show="open" x-cloak class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            <span class="sr-only">Toggle navigation</span>
          </button>
        </div>
      </div>

      <div id="primary-navigation" x-bind:class="{ 'block': open, 'hidden': ! open }" class="hidden overflow-hidden transition-all duration-300 basis-full grow md:block" aria-labelledby="primary-navigation-toggle">
        <div class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
          <div class="py-2 md:py-0 flex flex-col md:flex-row md:items-center md:justify-end gap-0.5 md:gap-1">
            
            @php
                $cmsMenu = app(\Liberu\Cms\Menus\Contracts\MenuRepositoryInterface::class)->forLocation('header');
                $cmsMenuItems = $cmsMenu
                    ? $cmsMenu->items()->whereNull('parent_id')->get()
                    : collect();

                $isCurrentUrl = function (?string $url): bool {
                    $path = trim((string) parse_url((string) $url, PHP_URL_PATH), '/');

                    return request()->is($path === '' ? '/' : $path);
                };

                $navLinkClasses = 'p-2 flex items-center text-sm focus:outline-none focus:text-white hover:text-white';
            @endphp

            @forelse ($cmsMenuItems as $item)
                <a href="{{ $item->url }}"
                    @class([$navLinkClasses, 'text-white font-medium' => $isCurrentUrl($item->url), 'text-white/80' => ! $isCurrentUrl($item->url)])
                    @if ($isCurrentUrl($item->url)) aria-current="page" @endif>
                    {{ $item->label }}
                </a>
            @empty
                <a href="{{ url('/') }}"
                    @class([$navLinkClasses, 'text-white font-medium' => $isCurrentUrl('/'), 'text-white/80' => ! $isCurrentUrl('/')])
                    @if ($isCurrentUrl('/')) aria-current="page" @endif>Home</a>
                <a href="{{ url('/about') }}"
                    @class([$navLinkClasses, 'text-white font-medium' => $isCurrentUrl('/about'), 'text-white/80' => ! $isCurrentUrl('/about')])
                    @if ($isCurrentUrl('/about')) aria-current="page" @endif>About</a>
            @endforelse

            <div class="relative flex flex-wrap items-center gap-x-1.5 md:ps-2.5 mt-1 md:mt-0 md:ms-1.5 before:block before:absolute before:top-1/2 before:-start-px before:w-px before:h-4 before:bg-white/30 before:-translate-y-1/2">
                @if (auth()->check())
                    @php
                        $user = auth()->user();
                        $role = $user->hasRole('admin') ? 'admin' : 'user';
                        $dashboardUrl = '/admin';
                    @endphp

                    <a href="{{ url($dashboardUrl) }}"
                        class="p-2 w-full flex items-center text-sm text-white/80 hover:text-white focus:outline-none focus:text-white">
                        <svg class="shrink-0 size-4 me-3 md:me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                         <path d="M5 3a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Zm14 18a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h4ZM5 11a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H5Zm14 2a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h4Z"/>
                        </svg>
                        {{ ucfirst($role) }} Dashboard
                    </a>
                @else
                    <a href="{{ url('/admin/login') }}"
                        class="p-2  flex items-center text-sm text-white/80 hover:text-white focus:outline-none focus:text-white">
                        <svg class="shrink-0 size-4 me-3 md:me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Login
                    </a>
                    <a href="{{ url('/admin/register') }}"
                        class="p-2  flex items-center text-sm text-white/80 hover:text-white focus:outline-none focus:text-white">
                        <svg class="shrink-0 size-4 me-3 md:me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H7Zm8-1a1 1 0 0 1 1-1h1v-1a1 1 0 1 1 2 0v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0v-1h-1a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                        </svg>
                        Register
                    </a>
                @endif
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>