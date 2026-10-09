<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') | {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <main>
            @include('components.notification')
            @include('components.navbar', ['fixed' => true])

            <div class="pt-17 min-[1100px]:pt-20 grid gap-2 relative">
                @include('components.sidebar')
                <div class="wrapper-fluid py-3 sm:py-5 text-(--color-primary)">
                    <div class="min-[1100px]:ps-62.5">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>

        @stack('after-scripts')
    </body>
</html>
