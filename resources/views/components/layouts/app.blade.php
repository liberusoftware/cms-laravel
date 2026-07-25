<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ app(\App\Settings\GeneralSettings::class)->site_name }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @livewireStyles
        @vite('resources/css/app.css')

        @stack('head')
    </head>

    <body class="antialiased">

    <section>
        <x-navigation />
            {{ $slot }}
        <x-footer />
    </section>

    @livewire('notifications')
    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>
