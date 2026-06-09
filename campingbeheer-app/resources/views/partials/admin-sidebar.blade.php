<div class="flex min-h-screen w-60 flex-col border-r border-border bg-surface">
    <div class="flex h-16 items-center justify-center border-b border-border">
        <h1 class="text-xl font-bold text-primary">Campingbeheer</h1>
    </div>

    <nav class="space-y-1 p-4">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-accent text-white' : 'text-muted hover:bg-secondary hover:text-primary' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span data-i18n="admin.sidebar.dashboard">Dashboard</span>
        </a>

        <a href="{{ route('admin.accommodatie.index') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.accommodatie.*') ? 'bg-accent text-white' : 'text-muted hover:bg-secondary hover:text-primary' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span data-i18n="admin.sidebar.accommodations">Accommodatie</span>
        </a>

        <a href="{{ route('admin.planbord.index') }}"
            class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.planbord.*') ? 'bg-accent text-white' : 'text-muted hover:bg-secondary hover:text-primary' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span data-i18n="admin.sidebar.planning">Planbord</span>
        </a>
    </nav>
</div>
