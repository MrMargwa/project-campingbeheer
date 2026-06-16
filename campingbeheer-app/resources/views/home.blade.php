@extends('layouts.app')

@section('title', 'Home')

@section('content')

    <section class="space-y-10 mx-auto w-full">
        <div class="relative overflow-visible rounded-3xl border border-border bg-surface shadow-sm">
            <div class="relative h-56 bg-secondary bg-cover bg-center md:h-72 lg:h-80"
                style="background-image: url('{{ asset('images/camping.png') }}');">
                <div class="absolute inset-0 bg-primary/15"></div>
            </div>

            <div class="relative -mt-12 px-4 pb-4 sm:px-6 lg:px-10">
                <div class="mx-auto max-w-5xl rounded-[1.75rem] border border-border bg-surface p-4 shadow-lg sm:p-5">
                    <div class="grid gap-3 md:grid-cols-5 md:gap-2">
                        <div class="relative">
                            <label class="mb-1 block text-xs font-medium text-primary"
                                data-i18n="home.filter.travel_party">Reisgezelschap</label>

                            <input type="hidden" id="filter-personen" value="0">

                            <button id="persons-control" type="button"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-left text-sm text-primary flex items-center justify-between gap-2">
                                <span id="persons-summary" class="truncate"></span>
                                <svg id="persons-chevron" class="h-4 w-4 shrink-0 text-primary transition-transform"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>

                            <div id="persons-panel"
                                class="absolute left-0 right-0 z-10 mt-2 hidden rounded-lg border border-border bg-white p-4 shadow-lg">
                                <div class="space-y-3">
                                    <h4 class="text-lg font-semibold text-primary" data-i18n="home.filter.travel_party">
                                        Reisgezelschap</h4>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.adults_label">Personen v.a 14 jaar
                                            </div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.adults_desc">Aantal
                                                volwassenen</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" data-key="volwassenen"
                                                class="persons-btn persons-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-volwassenen" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="volwassenen"
                                                class="persons-btn persons-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.children_label">Kinderen 3 t/m 13
                                                jaar</div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.children_desc">Kinderen
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" data-key="kinderen"
                                                class="persons-btn persons-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-kinderen" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="kinderen"
                                                class="persons-btn persons-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.babies_label">Baby t/m 2 jaar</div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.babies_desc">Baby's</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" data-key="baby"
                                                class="persons-btn persons-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-baby" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="baby"
                                                class="persons-btn persons-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button id="persons-close" type="button"
                                            class="rounded-lg bg-surface border border-border px-3 py-1 text-sm"
                                            data-i18n="home.filter.done">Gereed</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="filter-type" class="mb-1 block text-xs font-medium text-primary"
                                data-i18n="home.filter.accommodation_type">Soort
                                verblijf</label>
                            <select id="filter-type"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="" data-i18n="home.filter.all_types">Alle soorten</option>
                                @foreach ($types as $typeSleutel => $typeRij)
                                    <option value="{{ $typeSleutel }}">{{ $typeRij->{'type_' . $taal} ?: $typeSleutel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filter-arrival" class="mb-1 block text-xs font-medium text-primary"
                                data-i18n="home.filter.arrival">Aankomst</label>
                            <input id="filter-arrival" type="date" min="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                        </div>

                        <div>
                            <label for="filter-departure" class="mb-1 block text-xs font-medium text-primary"
                                data-i18n="home.filter.departure">Vertrek</label>
                            <input id="filter-departure" type="date" min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                        </div>

                        <div>
                            <label for="filter-features" class="mb-1 block text-xs font-medium text-primary"
                                data-i18n="home.filter.features">Kenmerken</label>
                            <select id="filter-features"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="" data-i18n="home.filter.none_selected">Niets geselecteerd</option>
                                @foreach ($kenmerken as $kenmerk)
                                    <option value="{{ $kenmerk->name }}">{{ $kenmerk->translatedName($taal) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p id="date-error" class="hidden rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger"
            data-i18n="home.filter.date_error">
            Aankomst en vertrek mogen niet in het verleden liggen en vertrek moet minstens één dag na aankomst zijn.
        </p>

        <div id="filter-message" class="rounded-3xl border border-border bg-surface p-5 shadow-sm sm:p-6">
            <p class="text-sm font-medium text-muted" data-i18n="home.filter.please_filter">U moet eerst nog filteren.</p>
            <p class="mt-2 text-lg font-semibold text-primary" data-i18n="home.filter.choose_criteria">Kies
                reisgezelschap, soort verblijf of kenmerken om de
                verblijven te zien.</p>
        </div>

        <section id="results-wrapper" class="hidden space-y-5">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-primary sm:text-2xl" data-i18n="home.accommodations.title">
                    Verblijven</h2>
                <span id="results-count" class="text-sm text-muted"></span>
            </div>

            <div id="no-results" class="hidden rounded-2xl border border-border bg-white px-4 py-3 text-sm text-muted"
                data-i18n="home.no_results">
                Geen verblijven gevonden met deze filters.
            </div>

            <div id="results-list" class="space-y-6">
                @foreach ($accommodaties as $accommodatie)
                    <article
                        class="accommodation-card overflow-hidden rounded-3xl border border-border bg-surface shadow-sm"
                        data-type="{{ $accommodatie->type }}" data-persons="{{ $accommodatie->min_persons }}"
                        data-max-persons="{{ $accommodatie->max_persons }}"
                        data-features="{{ $accommodatie->features->pluck('name')->implode(' ') }}"
                        data-features-translated="{{ $accommodatie->features->map(fn($k) => $k->translatedName($taal))->implode(' ') }}"
                        data-id="{{ $accommodatie->id }}">
                        <div class="grid lg:grid-cols-2">
                            <div class="relative min-h-64 bg-secondary sm:min-h-72 lg:min-h-full">
                                @if ($accommodatie->image && file_exists(public_path('images/' . $accommodatie->image)))
                                    <img src="{{ asset('images/' . $accommodatie->image) }}"
                                        alt="{{ $accommodatie->translatedTitle($taal) }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <span
                                        class="absolute left-4 top-4 h-px w-[85%] rotate-48 origin-top-left bg-primary/80"></span>
                                    <span
                                        class="absolute left-4 top-4 h-px w-[85%] -rotate-48 origin-top-left bg-primary/80"></span>
                                @endif
                            </div>

                            <div class="flex flex-col justify-between p-6 sm:p-8">
                                <div class="space-y-4">
                                    <h3 class="text-2xl font-semibold text-primary">
                                        {{ $accommodatie->translatedTitle($taal) }}</h3>
                                    <p class="text-sm text-muted">{{ $accommodatie->translatedDescription($taal) }}</p>
                                    <ul class="space-y-2 text-sm text-muted">
                                        <li>{{ $accommodatie->min_persons }} - {{ $accommodatie->max_persons }} <span
                                                data-i18n="reserve.persons">personen</span></li>
                                        @if ($accommodatie->features->isNotEmpty())
                                            @foreach ($accommodatie->features as $kenmerk)
                                                <li>{{ $kenmerk->translatedName($taal) }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="text-lg font-semibold text-accent">&euro;{{ number_format($accommodatie->price_per_night, 2, ',', '.') }}</span>
                                        <span class="text-sm text-muted" data-i18n="home.accommodations.per_night">/
                                            nacht</span>
                                    </div>
                                    <a href="{{ route('reservation') }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-border bg-white px-5 py-2.5 text-sm font-medium text-primary transition hover:border-accent hover:text-accent"
                                        data-i18n="home.accommodations.book">Reserveer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </section>

    @include('partials.reserveer-modal')
@endsection

@section('scripts')
    <script>
        document.addEventListener('locale-changed', function() {
            location.reload();
        });
    </script>
    <script>
        (function() {
            const personenSelect = document.getElementById('filter-personen');
            const typeSelect = document.getElementById('filter-type');
            const kenmerkenSelect = document.getElementById('filter-features');
            const aankomstInput = document.getElementById('filter-arrival');
            const vertrekInput = document.getElementById('filter-departure');
            const datumFout = document.getElementById('date-error');
            const filterBericht = document.getElementById('filter-message');
            const resultatenWrapper = document.getElementById('results-wrapper');
            const resultatenTelling = document.getElementById('results-count');
            const geenResultaten = document.getElementById('no-results');
            const kaarten = document.querySelectorAll('.accommodation-card');
            const personenControle = document.getElementById('persons-control');
            const personenPaneel = document.getElementById('persons-panel');
            const personsChevron = document.getElementById('persons-chevron');
            const personenSamenvatting = document.getElementById('persons-summary');
            const personenSluit = document.getElementById('persons-close');

            const tellingen = {
                volwassenen: 0,
                kinderen: 0,
                baby: 0,
            };

            function naarDatumWaarde(date) {
                return new Date(date + 'T00:00:00');
            }

            function formatteerDatum(date) {
                return date.toISOString().slice(0, 10);
            }

            function plusEenDag(datumWaarde) {
                const volgendeDag = new Date(datumWaarde.getTime());
                volgendeDag.setDate(volgendeDag.getDate() + 1);
                return volgendeDag;
            }

            function minEenDag(datumWaarde) {
                const vorigeDag = new Date(datumWaarde.getTime());
                vorigeDag.setDate(vorigeDag.getDate() - 1);
                return vorigeDag;
            }

            function syncDatumGrenzen() {
                const vandaag = new Date();
                vandaag.setHours(0, 0, 0, 0);

                aankomstInput.min = formatteerDatum(vandaag);
                aankomstInput.max = '';
                vertrekInput.min = formatteerDatum(plusEenDag(vandaag));
                vertrekInput.max = '';

                if (aankomstInput.value) {
                    const aankomstDatum = naarDatumWaarde(aankomstInput.value);
                    const volgendeDag = plusEenDag(aankomstDatum);
                    vertrekInput.min = formatteerDatum(volgendeDag);

                    if (vertrekInput.value && naarDatumWaarde(vertrekInput.value) <= aankomstDatum) {
                        vertrekInput.value = '';
                    }
                } else {
                    vertrekInput.min = formatteerDatum(plusEenDag(vandaag));
                }

                if (vertrekInput.value) {
                    const vertrekDatum = naarDatumWaarde(vertrekInput.value);
                    const laatsteAankomstDag = minEenDag(vertrekDatum);
                    aankomstInput.max = formatteerDatum(laatsteAankomstDag);

                    if (aankomstInput.value && naarDatumWaarde(aankomstInput.value) >= vertrekDatum) {
                        aankomstInput.value = '';
                    }
                }
            }

            function valideerDatums() {
                syncDatumGrenzen();

                const vandaag = new Date();
                vandaag.setHours(0, 0, 0, 0);
                const aankomstWaarde = aankomstInput.value ? naarDatumWaarde(aankomstInput.value) : null;
                const vertrekWaarde = vertrekInput.value ? naarDatumWaarde(vertrekInput.value) : null;

                let heeftFout = false;

                if (aankomstWaarde && aankomstWaarde < vandaag) {
                    heeftFout = true;
                }

                if (vertrekWaarde && vertrekWaarde <= vandaag) {
                    heeftFout = true;
                }

                if (aankomstWaarde && vertrekWaarde && vertrekWaarde <= aankomstWaarde) {
                    heeftFout = true;
                }

                datumFout.classList.toggle('hidden', !heeftFout);

                return !heeftFout;
            }

            function getTotaalPersonen() {
                return tellingen.volwassenen + tellingen.kinderen + tellingen.baby;
            }

            function updatePersonenSamenvatting() {
                const totaal = getTotaalPersonen();
                personenSelect.value = totaal;
                personenSamenvatting.textContent = window.__('home.persons_' + (totaal === 1 ? 'one' : 'other'), {
                    count: totaal
                });
            }

            function wisselPaneel(open) {
                personenPaneel.classList.toggle('hidden', !open);
                personsChevron.classList.toggle('rotate-180', open);
            }

            function pasFiltersToe() {
                if (!valideerDatums()) return;

                const geselecteerdePersonen = parseInt(personenSelect.value, 10) || 0;
                const geselecteerdType = typeSelect.value;
                const geselecteerdKenmerk = kenmerkenSelect.value;

                let zichtbareTelling = 0;

                kaarten.forEach((kaart) => {
                    const minPersonen = parseInt(kaart.dataset.persons || '0', 10);
                    const matchPersonen = !geselecteerdePersonen || geselecteerdePersonen >= minPersonen;
                    const matchType = !geselecteerdType || kaart.dataset.type === geselecteerdType;
                    const matchKenmerk = !geselecteerdKenmerk || (kaart.dataset.features || '').toLowerCase()
                        .includes(geselecteerdKenmerk.toLowerCase());
                    const toonKaart = matchPersonen && matchType && matchKenmerk;

                    kaart.classList.toggle('hidden', !toonKaart);
                    if (toonKaart) zichtbareTelling++;
                });

                filterBericht.classList.add('hidden');
                resultatenWrapper.classList.remove('hidden');
                resultatenTelling.textContent = window.__('home.results_' + (zichtbareTelling === 1 ? 'one' : 'other'), {
                    count: zichtbareTelling
                });
                geenResultaten.classList.toggle('hidden', zichtbareTelling > 0);
                geenResultaten.textContent = zichtbareTelling > 0 ? '' : window.__('home.no_results');
            }

            personenControle.addEventListener('click', function(e) {
                e.stopPropagation();
                wisselPaneel(personenPaneel.classList.contains('hidden'));
            });

            personenSluit.addEventListener('click', function() {
                wisselPaneel(false);
            });

            document.addEventListener('click', function(e) {
                if (!personenControle.contains(e.target) && !personenPaneel.contains(e.target)) {
                    wisselPaneel(false);
                }
            });

            document.querySelectorAll('.persons-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const sleutel = btn.dataset.key;
                    const isInc = btn.classList.contains('persons-increment');
                    const el = document.getElementById('count-' + sleutel);

                    if (isInc) {
                        tellingen[sleutel]++;
                    } else {
                        if (tellingen[sleutel] <= 0) return;
                        tellingen[sleutel]--;
                    }

                    el.textContent = tellingen[sleutel];
                    updatePersonenSamenvatting();
                    pasFiltersToe();
                });
            });

            [typeSelect, kenmerkenSelect, aankomstInput, vertrekInput].forEach(el => {
                if (!el) return;
                el.addEventListener('change', pasFiltersToe);
            });

            syncDatumGrenzen();
            updatePersonenSamenvatting();
            pasFiltersToe();
        })();
    </script>
@endsection
