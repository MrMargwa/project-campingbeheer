<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Campingbeheer - Admin Dashboard'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-primary text-primary">
    <div class="flex h-full">
        {{-- Sidebar --}}
        @include('partials.admin-sidebar')

        {{-- Main content --}}
        <main class="flex-1 overflow-y-auto">
            @include('partials.admin-header')
            @yield('content')
        </main>
    </div>
</body>

</html>
