<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Campingbeheer'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-primary text-slate-900">
    @include('partials.nav')

    <main class="mx-auto max-w-6xl px-6 py-10 min-h-screen">
        @yield('content')
    </main>

    @include('partials.footer')

    @yield('scripts')
</body>

</html>
