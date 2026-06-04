{{-- <div class="w-full flex justify-between bg-surface px-12 py-4 border-b-1 shadow-sm">
    <div>
        @if (Route::is('admin'))
        <h2 class="text-lg font-semibold">Admin Dashboard</h2>
        @elseif(Route::is('admin.accommodatie.*'))
        <h2 class="text-lg font-semibold">Accommodatie</h2>
        @elseif(Route::is('admin.planbord.*'))
        <h2 class="text-lg font-semibold">Planbord</h2>
        @endif
    </div>

    <div>
        <button class="bg-danger hover:bg-danger-hover p-1.5 roundend-md shadow-md text-primary">
            Uitloggen
        </button>
    </div>
</div> --}}

<div class="bg-surface border-b border-border">
    <div class="w-full px-8 lg:px-12">
        <div class="flex items-center justify-between h-16">

            @if (Route::is('admin'))
                <h2 class="text-lg font-semibold">Admin Dashboard</h2>
            @elseif(Route::is('admin.accommodatie.*'))
                <h2 class="text-lg font-semibold">Accommodatie</h2>
            @elseif(Route::is('admin.planbord.*'))
                <h2 class="text-lg font-semibold">Planbord</h2>
            @endif

            <div>
                <a href="/"
                    class="bg-danger hover:bg-danger-hover text-white font-medium px-5 py-2 rounded-md transition">
                    Uitloggen
                </a>
            </div>
        </div>
    </div>
</div>