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
                <script>
                    (function() {
                        var SEL = ['nl','en','de','fy'];
                        var NAMES = {nl:'Nederlands',en:'English',de:'Deutsch',fy:'Frysk'};

                        function flagUrl(code) {
                            return '/images/flags/'+code+'.svg';
                        }

                        window.renderLangMenu = function(activeLocale) {
                            var menu = document.getElementById('lang-dropdown-menu');
                            if (!menu) return;
                            menu.innerHTML = SEL.map(function(code){
                                return '<button type="button" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-left text-primary hover:bg-gray-50 transition border-b border-border last:border-b-0" data-lang="'+code+'" onclick="window.i18nSetLocale(\''+code+'\');closeLangDropdown()">'+
                                    '<img src="'+flagUrl(code)+'" alt="'+code+'" class="w-5 h-auto rounded-sm">'+
                                    '<span>'+NAMES[code]+'</span>'+
                                    (code===activeLocale?'<span class="ml-auto text-accent text-xs">&#10003;</span>':'')+
                                '</button>';
                            }).join('');
                        };

                        var locale = (function(){
                            var s = localStorage.getItem('locale');
                            if (s && SEL.indexOf(s)!==-1) return s;
                            var lang = (navigator.language||'').slice(0,2);
                            if (lang==='nl'||lang==='de') return lang;
                            if ((navigator.language||'').toLowerCase()==='fy'||(navigator.languages||[]).some(function(l){return l.toLowerCase()==='fy'||l.toLowerCase()==='fy-nl'})) return 'fy';
                            return 'en';
                        })();

                        document.cookie = 'locale=' + locale + ';path=/;SameSite=Lax';

                        var sel = document.getElementById('language-select');
                        if (sel) sel.value = locale;

                        var btn = document.getElementById('lang-dropdown-btn');
                        if (btn) btn.innerHTML = '<img src="'+flagUrl(locale)+'" alt="'+locale+'" class="w-5 h-auto rounded-sm">'+
                            '<svg class="w-4 h-4 text-primary transition-transform" id="lang-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';

                        window.renderLangMenu(locale);
                    })();

                    function toggleLangDropdown(e) {
                        e.stopPropagation();
                        var menu = document.getElementById('lang-dropdown-menu');
                        var ch = document.getElementById('lang-chevron');
                        var isOpen = !menu.classList.contains('hidden');
                        menu.classList.toggle('hidden', isOpen);
                        if (ch) ch.classList.toggle('rotate-180', !isOpen);
                    }

                    function closeLangDropdown() {
                        var menu = document.getElementById('lang-dropdown-menu');
                        var ch = document.getElementById('lang-chevron');
                        menu.classList.add('hidden');
                        if (ch) ch.classList.remove('rotate-180');
                    }

                    document.addEventListener('click', function(e) {
                        var wrapper = document.getElementById('lang-wrapper');
                        if (wrapper && !wrapper.contains(e.target)) {
                            closeLangDropdown();
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</nav>
