@extends('layouts.app')

@section('title', 'Reserveren')

@section('content')
    {{-- Hero Banner --}}
    <section class="-mx-6 -mt-10 mb-12 relative overflow-hidden">
        <div class="h-48 md:h-64 bg-gradient-to-br from-accent/80 to-accent/60 flex items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight">Reserveren</h1>
        </div>
    </section>

    {{-- Main content: plattegrond links, filter rechts --}}
    <section class="flex flex-col lg:flex-row gap-8">
        {{-- Links: Plattegrond --}}
        <div class="flex-1">
            <div class="relative bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
                <img src="{{ asset('images/plattegrond.png') }}" usemap="#plattegrond" alt="Plattegrond camping"
                    class="w-full h-auto" id="kaart-img">

                <map name="plattegrond" id="plattegrond-map">
                    @foreach ($accommodaties as $accommodatie)
                        <area shape="rect" coords="0,0,0,0" data-id="{{ $accommodatie->id }}"
                            data-type="{{ $accommodatie->type }}" data-status="{{ $accommodatie->status }}"
                            data-titel="{{ $accommodatie->titel }}" data-prijs="{{ $accommodatie->prijs_per_nacht }}"
                            data-min="{{ $accommodatie->min_personen }}" data-max="{{ $accommodatie->max_personen }}"
                            href="#" data-orig-coords="{{ $accommodatie->coords ?? '0,0,0,0' }}">
                    @endforeach
                </map>

                {{-- Tooltip --}}
                <div id="tooltip"
                    class="hidden fixed z-50 w-56 bg-surface shadow-xl rounded-lg border border-border overflow-hidden pointer-events-none">
                    <div class="p-3" id="tooltip-content"></div>
                </div>

                {{-- Placeholder melding --}}
                <div class="absolute inset-0 flex items-center justify-center bg-secondary/50" id="placeholder-msg">
                    <div class="text-center text-muted">
                        <p class="text-lg font-medium">Plattegrond niet gevonden</p>
                        <p class="text-sm">Plaats een afbeelding in <code
                                class="text-accent">public/images/plattegrond.png</code></p>
                    </div>
                </div>
            </div>

            {{-- Geselecteerde accommodatie info --}}
            <div id="geselecteerd-info" class="mt-6 hidden">
                <div class="p-4 bg-surface border border-border rounded-xl shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-primary" id="sel-titel"></h3>
                            <p class="text-sm text-muted" id="sel-type"></p>
                            <p class="text-sm text-muted mt-1" id="sel-beschrijving"></p>
                            <p class="text-sm mt-2">
                                <span class="text-muted" id="sel-personen"></span>
                                <span class="mx-2 text-border">|</span>
                                <span class="font-semibold text-accent" id="sel-prijs"></span>
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium" id="sel-status-badge"></span>
                    </div>
                    <button id="sel-reserveer-btn"
                        class="mt-4 w-full bg-accent hover:bg-accent-hover text-white font-medium px-5 py-2.5 rounded-lg transition text-sm hidden">
                        Direct Reserveren
                    </button>
                </div>
            </div>
        </div>

        {{-- Rechts: Filter --}}
        <div class="w-full lg:w-72 shrink-0">
            <div class="bg-surface border border-border rounded-xl shadow-sm p-5 sticky top-6">
                <h2 class="font-semibold text-primary mb-4">Filter</h2>

                {{-- Type filter --}}
                <div class="space-y-2 mb-6" id="filter-types">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="filter-type" value="alle" checked class="accent-accent">
                        <span class="text-sm text-primary">Alle types</span>
                        <span class="text-xs text-muted ml-auto" id="aantal-alle"></span>
                    </label>
                </div>

                {{-- Status filter --}}
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-primary mb-2">Status</h3>
                    <select id="filter-status"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm bg-surface text-primary focus:outline-none focus:ring-2 focus:ring-accent/50">
                        <option value="alle">Alle</option>
                        <option value="beschikbaar">Beschikbaar</option>
                        <option value="niet_beschikbaar">Niet beschikbaar</option>
                    </select>
                </div>

                {{-- Resultaat telling --}}
                <div class="border-t border-border pt-4">
                    <p class="text-sm text-muted" id="resultaat-telling"></p>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function() {
            const accommodaties = @json($accommodaties);
            const types = [...new Set(accommodaties.map(a => a.type))];
            const areas = document.querySelectorAll('#plattegrond-map area');
            const img = document.getElementById('kaart-img');
            const tooltip = document.getElementById('tooltip');
            const tooltipContent = document.getElementById('tooltip-content');
            const placeholderMsg = document.getElementById('placeholder-msg');
            const geselecteerdDiv = document.getElementById('geselecteerd-info');

            // Filter elementen
            const filterTypeRadios = document.querySelectorAll('input[name="filter-type"]');
            const filterStatusSelect = document.getElementById('filter-status');
            const resultaatTelling = document.getElementById('resultaat-telling');

            // ---- Filter types opbouwen ----
            const filterTypesDiv = document.getElementById('filter-types');
            const aantalAlleSpan = document.getElementById('aantal-alle');
            aantalAlleSpan.textContent = accommodaties.length;

            types.forEach(type => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-2 cursor-pointer';
                const count = accommodaties.filter(a => a.type === type).length;
                label.innerHTML = `
                <input type="radio" name="filter-type" value="${type}" class="accent-accent">
                <span class="text-sm text-primary capitalize">${type}</span>
                <span class="text-xs text-muted ml-auto">${count}</span>
            `;
                filterTypesDiv.appendChild(label);
            });

            // ---- Image map coördinaten schalen ----
            function schaalCoordinaten() {
                if (!img || !img.naturalWidth) return;
                const scaleX = img.clientWidth / img.naturalWidth;
                const scaleY = img.clientHeight / img.naturalHeight;

                areas.forEach(area => {
                    const orig = area.dataset.origCoords || '0,0,0,0';
                    area.dataset.origCoords = orig;
                    const coords = orig.split(',').map(Number);
                    if (coords.length === 4) {
                        area.coords = [
                            Math.round(coords[0] * scaleX),
                            Math.round(coords[1] * scaleY),
                            Math.round(coords[2] * scaleX),
                            Math.round(coords[3] * scaleY),
                        ].join(',');
                    }
                });
            }

            // ---- Tooltip ----
            function toonTooltip(e, data) {
                const statusLabel = data.dataset.status === 'beschikbaar' ? 'Vrij' : 'Bezet';
                const statusClass = data.dataset.status === 'beschikbaar' ? 'text-accent bg-accent/10' :
                    'text-danger bg-danger/10';
                const prijs = parseFloat(data.dataset.prijs).toFixed(2);

                tooltipContent.innerHTML = `
                <div class="flex items-center justify-between mb-1">
                    <strong class="text-sm text-primary">${data.dataset.titel}</strong>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium ${statusClass}">${statusLabel}</span>
                </div>
                <p class="text-xs text-muted mb-1">${data.dataset.type}</p>
                <p class="text-xs text-muted">${data.dataset.min}-${data.dataset.max} personen</p>
                <p class="text-sm font-semibold text-accent mt-1">&euro;${prijs} <span class="text-xs text-muted font-normal">/ nacht</span></p>
            `;
                tooltip.classList.remove('hidden');
            }

            function verbergTooltip() {
                tooltip.classList.add('hidden');
            }

            function verplaatsTooltip(e) {
                let x = e.clientX + 15;
                let y = e.clientY + 15;
                const tw = tooltip.offsetWidth;
                const th = tooltip.offsetHeight;
                if (x + tw > window.innerWidth) x = e.clientX - tw - 15;
                if (y + th > window.innerHeight) y = e.clientY - th - 15;
                tooltip.style.left = x + 'px';
                tooltip.style.top = y + 'px';
            }

            // ---- Selecteer accommodatie ----
            function selecteerAccommodatie(id) {
                const acc = accommodaties.find(a => a.id == id);
                if (!acc) return;

                document.getElementById('sel-titel').textContent = acc.titel;
                document.getElementById('sel-type').textContent = acc.type;
                document.getElementById('sel-beschrijving').textContent = acc.beschrijving || '';
                document.getElementById('sel-personen').textContent = acc.min_personen + ' - ' + acc.max_personen +
                    ' personen';
                document.getElementById('sel-prijs').textContent = '€' + parseFloat(acc.prijs_per_nacht).toFixed(2) +
                    ' / nacht';

                const badge = document.getElementById('sel-status-badge');
                const btn = document.getElementById('sel-reserveer-btn');
                if (acc.status === 'beschikbaar') {
                    badge.textContent = 'Beschikbaar';
                    badge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-accent/10 text-accent';
                    btn.classList.remove('hidden');
                } else {
                    badge.textContent = 'Niet beschikbaar';
                    badge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-danger/10 text-danger';
                    btn.classList.add('hidden');
                }

                geselecteerdDiv.classList.remove('hidden');
                geselecteerdDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }

            // ---- Filter toepassen ----
            function pasFilterToe() {
                const selectedType = document.querySelector('input[name="filter-type"]:checked').value;
                const selectedStatus = filterStatusSelect.value;

                let getoondeCount = 0;

                areas.forEach((area, index) => {
                    const acc = accommodaties[index];
                    if (!acc) return;

                    const matchType = selectedType === 'alle' || acc.type === selectedType;
                    const matchStatus = selectedStatus === 'alle' || acc.status === selectedStatus;
                    const toon = matchType && matchStatus;

                    if (toon) {
                        area.style.display = '';
                        getoondeCount++;
                    } else {
                        area.style.display = 'none';
                    }
                });

                resultaatTelling.textContent = getoondeCount === 1 ?
                    '1 accommodatie getoond' :
                    getoondeCount + ' accommodaties getoond';
            }

            // ---- Events ----
            areas.forEach(area => {
                area.addEventListener('mouseenter', toonTooltip);
                area.addEventListener('mouseleave', verbergTooltip);
                area.addEventListener('mousemove', verplaatsTooltip);
                area.addEventListener('click', function(e) {
                    e.preventDefault();
                    selecteerAccommodatie(this.dataset.id);
                });
            });

            filterTypeRadios.forEach(radio => {
                radio.addEventListener('change', pasFilterToe);
            });
            filterStatusSelect.addEventListener('change', pasFilterToe);

            // ---- Init bij laden ----
            if (img.complete && img.naturalWidth > 0) {
                if (placeholderMsg) placeholderMsg.style.display = 'none';
                schaalCoordinaten();
                pasFilterToe();
            } else {
                img.addEventListener('load', function() {
                    if (placeholderMsg) placeholderMsg.style.display = 'none';
                    schaalCoordinaten();
                    pasFilterToe();
                });
            }

            window.addEventListener('resize', schaalCoordinaten);
        })();
    </script>
@endsection
