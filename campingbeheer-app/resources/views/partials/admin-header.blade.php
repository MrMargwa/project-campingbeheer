{{-- <div class="w-full flex justify-between bg-surface px-12 py-4 border-b-1 shadow-sm">
    <div>
        @if (Route::is('admin'))
        <h2 class="text-lg font-semibold">Admin Dashboard</h2>
        @elseif(Route::is('admin.accommodation.*'))
        <h2 class="text-lg font-semibold">Accommodatie</h2>
        @elseif(Route::is('admin.planning-board.*'))
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
    <div class="w-full px-4 lg:px-6">
        <div class="flex items-center h-16">

            @if (Route::is('admin'))
                <h2 class="text-lg font-semibold" data-i18n="admin.header.dashboard">Dashboard</h2>
            @elseif(Route::is('admin.accommodation.*'))
                <h2 class="text-lg font-semibold" data-i18n="admin.header.accommodations">Accommodatie</h2>
            @elseif(Route::is('admin.planning-board.*'))
                <h2 class="text-lg font-semibold" data-i18n="admin.header.planning">Planbord</h2>
            @endif

            <div class="flex items-center gap-3 ml-auto">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-danger hover:bg-danger-hover text-white font-medium px-5 py-2 rounded-md transition cursor-pointer border-0" data-i18n="admin.header.logout">
                        Uitloggen
                    </button>
                </form>
                <div class="relative" id="lang-wrapper">
                    <button id="lang-dropdown-btn" type="button"
                        class="flex items-center gap-2 border border-border rounded-md px-3 py-2 text-sm text-primary bg-white cursor-pointer hover:bg-gray-50 transition"
                        onclick="toggleLangDropdown(event)">
                        <img src="/images/flags/nl.svg" alt="nl" class="w-5 h-auto rounded-sm">
                        <svg class="w-4 h-4 text-primary transition-transform" id="lang-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div id="lang-dropdown-menu"
                        class="absolute right-0 mt-1 min-w-[160px] hidden rounded-lg border border-border bg-white shadow-lg z-50 overflow-hidden">
                    </div>
                </div>
                <select id="language-select" class="hidden">
                    <option value="nl">NL</option>
                    <option value="en">EN</option>
                    <option value="de">DE</option>
                    <option value="fy">FY</option>
                </select>
            </div>
        </div>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded', function(){ window.i18nInitDropdown && window.i18nInitDropdown(); });</script>