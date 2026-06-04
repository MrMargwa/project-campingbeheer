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
            <div class="relative bg-surface rounded-xl shadow-sm border border-border overflow-hidden" id="kaart-container">
                <img src="{{ asset('images/plattegrond.png') }}" alt="Plattegrond camping" class="w-full h-auto"
                    id="kaart-img">

                {{-- Dots overlay --}}
                <div id="dots-overlay" class="absolute inset-0"></div>

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
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-primary" id="sel-titel"></h3>
                            <p class="text-sm text-muted capitalize" id="sel-type"></p>
                            <p class="text-sm text-muted mt-1" id="sel-beschrijving"></p>
                            <p class="text-sm mt-2">
                                <span class="text-muted" id="sel-personen"></span>
                                <span class="mx-2 text-border">|</span>
                                <span class="font-semibold text-accent" id="sel-prijs"></span>
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium self-start" id="sel-status-badge"></span>
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

                <div class="space-y-2 mb-6" id="filter-types">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="filter-type" value="alle" checked class="accent-accent">
                        <span class="text-sm text-primary">Alle types</span>
                        <span class="text-xs text-muted ml-auto" id="aantal-alle"></span>
                    </label>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-medium text-primary mb-2">Status</h3>
                    <select id="filter-status"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm bg-surface text-primary focus:outline-none focus:ring-2 focus:ring-accent/50">
                        <option value="alle">Alle</option>
                        <option value="beschikbaar">Beschikbaar</option>
                        <option value="niet_beschikbaar">Niet beschikbaar</option>
                    </select>
                </div>

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
            // ============================================================
            // ZONE POLYGONEN — overgenomen van image-map.net
            // ============================================================
            const ZONES = {
                'Blokhut': [331, 333, 230, 391, 363, 649, 499, 604],
                'Camperplaats': [554, 239, 768, 38, 1005, 400, 937, 499, 866, 672],
                'Vakantiewoning': [418, 425, 537, 348, 612, 461, 494, 537],
                'Safaritent': [491, 68, 583, 201, 759, 30, 709, -1],
                'Chalet': [487, 75, 575, 203, 342, 322, 274, 205],
            };

            const accommodaties = @json($accommodaties);
            const types = [...new Set(accommodaties.map(a => a.type))];
            const overlay = document.getElementById('dots-overlay');
            const img = document.getElementById('kaart-img');
            const container = document.getElementById('kaart-container');
            const tooltip = document.getElementById('tooltip');
            const tooltipContent = document.getElementById('tooltip-content');
            const placeholderMsg = document.getElementById('placeholder-msg');
            const geselecteerdDiv = document.getElementById('geselecteerd-info');
            const filterStatusSelect = document.getElementById('filter-status');
            const resultaatTelling = document.getElementById('resultaat-telling');
            const filterTypesDiv = document.getElementById('filter-types');
            const aantalAlleSpan = document.getElementById('aantal-alle');

            let imgW = 0,
                imgH = 0; // natural dimensions
            let dots = [];

            // ---- Filter types opbouwen ----
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
            const filterTypeRadios = document.querySelectorAll('input[name="filter-type"]');

            // ---- Hulpfuncties ----
            function perType(type) {
                return accommodaties.filter(a => a.type === type);
            }

            // ---- Point-in-polygon (ray casting) ----
            function isInPolygon(px, py, poly) {
                let inside = false;
                for (let i = 0, j = poly.length - 2; i < poly.length; j = i, i += 2) {
                    const xi = poly[i],
                        yi = poly[i + 1];
                    const xj = poly[j],
                        yj = poly[j + 1];
                    if ((yi > py) !== (yj > py) && px < (xj - xi) * (py - yi) / (yj - yi) + xi) {
                        inside = !inside;
                    }
                }
                return inside;
            }

            // ---- Random punt in polygoon (rejection sampling) ----
            function randomPuntInPolygoon(poly, maxPogingen) {
                const xs = [],
                    ys = [];
                for (let i = 0; i < poly.length; i += 2) {
                    xs.push(poly[i]);
                    ys.push(poly[i + 1]);
                }
                const minX = Math.min(...xs),
                    maxX = Math.max(...xs);
                const minY = Math.min(...ys),
                    maxY = Math.max(...ys);
                const cx = (minX + maxX) / 2;
                const cy = (minY + maxY) / 2;

                for (let poging = 0; poging < maxPogingen; poging++) {
                    const px = minX + Math.random() * (maxX - minX);
                    const py = minY + Math.random() * (maxY - minY);
                    if (isInPolygon(px, py, poly)) {
                        return {
                            x: Math.round(px),
                            y: Math.round(py)
                        };
                    }
                }
                return {
                    x: Math.round(cx),
                    y: Math.round(cy)
                };
            }

            // ---- Random punten in polygoon genereren ----
            function verdeelPunten(type, aantal) {
                const poly = ZONES[type];
                if (!poly) return [];

                const punten = [];
                for (let i = 0; i < aantal; i++) {
                    const p = randomPuntInPolygoon(poly, 100);
                    punten.push(p);
                }
                return punten;
            }

            // ---- Dots aanmaken ----
            function maakDots() {
                overlay.innerHTML = '';
                dots = [];

                if (!imgW || !imgH) return;

                types.forEach(type => {
                    const items = perType(type);
                    if (items.length === 0) return;
                    const punten = verdeelPunten(type, items.length);

                    items.forEach((acc, i) => {
                        const p = punten[i] || {
                            x: 50,
                            y: 50
                        };
                        const pctX = (p.x / imgW) * 100;
                        const pctY = (p.y / imgH) * 100;

                        const dot = document.createElement('div');
                        const kleur = acc.status === 'beschikbaar' ? 'bg-green-500' : 'bg-red-500';
                        dot.className =
                            `dot absolute w-3.5 h-3.5 -ml-[7px] -mt-[7px] rounded-full ${kleur} border-2 border-white shadow-md cursor-pointer transition-transform hover:scale-150 hover:z-10`;
                        dot.style.left = pctX + '%';
                        dot.style.top = pctY + '%';

                        dot.dataset.id = acc.id;
                        dot.dataset.type = acc.type;
                        dot.dataset.status = acc.status;
                        dot.dataset.titel = acc.titel;
                        dot.dataset.prijs = acc.prijs_per_nacht;
                        dot.dataset.min = acc.min_personen;
                        dot.dataset.max = acc.max_personen;
                        dot.dataset.beschrijving = acc.beschrijving || '';

                        dot.addEventListener('mouseenter', toonTooltip);
                        dot.addEventListener('mouseleave', verbergTooltip);
                        dot.addEventListener('mousemove', verplaatsTooltip);
                        dot.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selecteerAccommodatie(acc);
                        });

                        overlay.appendChild(dot);
                        dots.push(dot);
                    });
                });

                pasFilterToe();
            }

            // ---- Tooltip ----
            function toonTooltip(e) {
                const d = e.currentTarget.dataset;
                const statusLabel = d.status === 'beschikbaar' ? 'Vrij' : 'Bezet';
                const statusClass = d.status === 'beschikbaar' ? 'text-accent bg-accent/10' :
                    'text-danger bg-danger/10';
                const prijs = parseFloat(d.prijs).toFixed(2);

                tooltipContent.innerHTML = `
                <div class="flex items-center justify-between mb-1">
                    <strong class="text-sm text-primary">${d.titel}</strong>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium ${statusClass}">${statusLabel}</span>
                </div>
                <p class="text-xs text-muted capitalize">${d.type}</p>
                <p class="text-xs text-muted">${d.min}-${d.max} personen</p>
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
            function selecteerAccommodatie(acc) {
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

            // ---- Filter ----
            function pasFilterToe() {
                const selectedType = document.querySelector('input[name="filter-type"]:checked')?.value || 'alle';
                const selectedStatus = filterStatusSelect.value;

                let getoondeCount = 0;

                dots.forEach(dot => {
                    const acc = accommodaties.find(a => a.id == dot.dataset.id);
                    if (!acc) return;

                    const matchType = selectedType === 'alle' || acc.type === selectedType;
                    const matchStatus = selectedStatus === 'alle' || acc.status === selectedStatus;
                    const toon = matchType && matchStatus;

                    dot.style.display = toon ? '' : 'none';
                    if (toon) getoondeCount++;
                });

                resultaatTelling.textContent = getoondeCount === 1 ?
                    '1 accommodatie getoond' :
                    getoondeCount + ' accommodaties getoond';
            }

            // ---- Events ----
            filterTypeRadios.forEach(r => r.addEventListener('change', pasFilterToe));
            filterStatusSelect.addEventListener('change', pasFilterToe);

            // ---- Init ----
            function init() {
                if (!img.complete || !img.naturalWidth) {
                    img.addEventListener('load', init);
                    return;
                }

                imgW = img.naturalWidth;
                imgH = img.naturalHeight;

                if (placeholderMsg) placeholderMsg.style.display = 'none';
                maakDots();
            }

            init();
            window.addEventListener('resize', () => {}); // dots use %, no recalculation needed
        })();
    </script>
@endsection
