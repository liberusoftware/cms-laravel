@props([
    'title' => null,
])
@php($siteName = app(\App\Settings\GeneralSettings::class)->site_name)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title ? $title.' — '.$siteName : $siteName }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @livewireStyles
        @vite('resources/css/app.css')

        @stack('head')
    </head>

    <body class="min-h-screen flex flex-col bg-white text-gray-900 antialiased">
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:start-2 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-blue-700 focus:shadow"
        >
            Skip to content
        </a>

        <x-navigation />

        <main id="main-content" class="grow">
            {{ $slot }}
        </main>

        <x-footer />

        @livewire('notifications')
        @livewireScripts
        @vite('resources/js/app.js')
    </body>
</html>
