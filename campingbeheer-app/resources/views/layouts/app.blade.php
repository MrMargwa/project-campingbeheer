<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Campingbeheer'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="min-h-screen bg-primary text-primary">
    @include('partials.nav')

    <main class="w-full py-10 px-32 xl:px-56 min-h-screen">
        @yield('content')
    </main>

    @include('partials.footer')

    @yield('scripts')
</body>

</html>
