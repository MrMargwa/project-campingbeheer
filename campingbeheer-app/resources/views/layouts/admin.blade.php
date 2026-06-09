@php
    $supported = ['nl', 'en', 'de', 'fy'];
    $locale = request()->cookie('locale', 'nl');
    if (!in_array($locale, $supported, true))
        $locale = 'nl';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Campingbeheer - Admin Dashboard'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        document.addEventListener('locale-changed', function () { location.reload(); });
    </script>
</head>

<body class="min-h-screen bg-primary text-primary">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        @include('partials.admin-sidebar')

        {{-- Main content --}}
        <main class="flex-1 overflow-y-auto">
            @include('partials.admin-header')
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>

</html>