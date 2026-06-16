<nav class="bg-surface border-b border-border">
    <div class="w-full px-4 lg:px-6">
        <div class="flex items-center h-16">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-3">
                <div>
                    <h1 class="text-primary font-semibold text-lg">
                        Campingbeheer
                    </h1>
                </div>
            </a>

            <!-- Reserveer Knop + Taal -->
            <div class="flex items-center gap-3 ml-auto">
                <a href="/reserveren"
                    class="bg-accent hover:bg-accent-hover text-white font-medium px-5 py-2 rounded-md transition"
                    data-i18n="nav.book_now">
                    Reserveer Nu
                </a>
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
                <script>window.i18nInitDropdown && window.i18nInitDropdown();</script>
            </div>
        </div>
    </div>
</nav>
