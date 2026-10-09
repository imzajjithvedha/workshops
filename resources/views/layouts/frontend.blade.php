<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') | {{ config('app.name') }}</title>
        <meta name="robots" content="noindex, nofollow">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="min-h-screen flex flex-col">
        <main class="flex-1 text-(--color-primary)">
            @include('components.notification')
            @include('components.navbar', ['fixed' => false])
            <div>
                @yield('content')
            </div>
        </main>
        @include('components.footer')

        @stack('after-scripts')
    </body>
</html>
