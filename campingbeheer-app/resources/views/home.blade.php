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
                            <label class="mb-1 block text-xs font-medium text-primary" data-i18n="home.filter.travel_party">Reisgezelschap</label>

                            <input type="hidden" id="filter-personen" value="0">

                            <button id="personen-control" type="button"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-left text-sm text-primary flex items-center justify-between gap-2">
                                <span id="personen-summary" class="truncate"></span>
                                <svg id="personen-chevron" class="h-4 w-4 shrink-0 text-primary transition-transform"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>

                            <div id="personen-panel"
                                class="absolute left-0 right-0 z-10 mt-2 hidden rounded-lg border border-border bg-white p-4 shadow-lg">
                                <div class="space-y-3">
                                    <h4 class="text-lg font-semibold text-primary" data-i18n="home.filter.travel_party">Reisgezelschap</h4>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.adults_label">Personen v.a 14 jaar</div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.adults_desc">Aantal volwassenen</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" data-key="adults"
                                                class="personen-btn personen-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-adults" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="adults"
                                                class="personen-btn personen-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.children_label">Kinderen 3 t/m 13 jaar</div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.children_desc">Kinderen</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" data-key="children"
                                                class="personen-btn personen-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-children" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="children"
                                                class="personen-btn personen-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.babies_label">Baby t/m 2 jaar</div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.babies_desc">Baby's</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" data-key="babies"
                                                class="personen-btn personen-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-babies" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="babies"
                                                class="personen-btn personen-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button id="personen-close" type="button"
                                            class="rounded-lg bg-surface border border-border px-3 py-1 text-sm" data-i18n="home.filter.done">Gereed</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="filter-type" class="mb-1 block text-xs font-medium text-primary" data-i18n="home.filter.accommodation_type">Soort
                                verblijf</label>
                            <select id="filter-type"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="" data-i18n="home.filter.all_types">Alle soorten</option>
                                @foreach ($types as $typeKey => $typeRow)
                                    <option value="{{ $typeKey }}">{{ $typeRow->{'type_' . $locale} ?: $typeKey }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filter-aankomst"
                                class="mb-1 block text-xs font-medium text-primary" data-i18n="home.filter.arrival">Aankomst</label>
                            <input id="filter-aankomst" type="date" min="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                        </div>

                        <div>
                            <label for="filter-vertrek" class="mb-1 block text-xs font-medium text-primary" data-i18n="home.filter.departure">Vertrek</label>
                            <input id="filter-vertrek" type="date" min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                        </div>

                        <div>
                            <label for="filter-huisdieren"
                                class="mb-1 block text-xs font-medium text-primary" data-i18n="home.filter.pets_label">Huisdieren</label>
                            <select id="filter-huisdieren"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="" data-i18n="home.filter.none_selected">Niets geselecteerd</option>
                                <option value="true" data-i18n="home.filter.pets_allowed">Huisdieren toegestaan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p id="date-error" class="hidden rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger" data-i18n="home.filter.date_error">
            Aankomst en vertrek mogen niet in het verleden liggen en vertrek moet minstens één dag na aankomst zijn.
        </p>

        <div id="filter-message" class="rounded-3xl border border-border bg-surface p-5 shadow-sm sm:p-6">
            <p class="text-sm font-medium text-muted" data-i18n="home.filter.please_filter">U moet eerst nog filteren.</p>
            <p class="mt-2 text-lg font-semibold text-primary" data-i18n="home.filter.choose_criteria">Kies reisgezelschap, soort verblijf of huisdieren om de
                verblijven te zien.</p>
        </div>

        <section id="results-wrapper" class="hidden space-y-5">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-primary sm:text-2xl" data-i18n="home.accommodations.title">Verblijven</h2>
                <span id="results-count" class="text-sm text-muted"></span>
            </div>

            <div id="no-results" class="hidden rounded-2xl border border-border bg-white px-4 py-3 text-sm text-muted" data-i18n="home.no_results">
                Geen verblijven gevonden met deze filters.
            </div>

            <div id="results-list" class="space-y-6">
                @foreach ($accommodaties as $accommodatie)
                    <article class="accommodatie-card overflow-hidden rounded-3xl border border-border bg-surface shadow-sm"
                        data-type="{{ $accommodatie->type }}" data-persons="{{ $accommodatie->min_personen }}"
                        data-max-persons="{{ $accommodatie->max_personen }}"
                        data-pets="{{ $accommodatie->huisdieren_toegestaan ? 'true' : 'false' }}"
                        data-id="{{ $accommodatie->id }}">
                        <div class="grid lg:grid-cols-2">
                            <div class="relative min-h-64 bg-secondary sm:min-h-72 lg:min-h-full">
                                @if ($accommodatie->afbeelding && file_exists(public_path('images/' . $accommodatie->afbeelding)))
                                    <img src="{{ asset('images/' . $accommodatie->afbeelding) }}" alt="{{ $accommodatie->vertaaldeTitel($locale) }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <span class="absolute left-4 top-4 h-px w-[85%] rotate-48 origin-top-left bg-primary/80"></span>
                                    <span
                                        class="absolute left-4 top-4 h-px w-[85%] -rotate-48 origin-top-left bg-primary/80"></span>
                                @endif
                            </div>

                            <div class="flex flex-col justify-between p-6 sm:p-8">
                                <div class="space-y-4">
                                    <h3 class="text-2xl font-semibold text-primary">{{ $accommodatie->vertaaldeTitel($locale) }}</h3>
                                    <p class="text-sm text-muted">{{ $accommodatie->vertaaldeBeschrijving($locale) }}</p>
                                    <ul class="space-y-2 text-sm text-muted">
                                        <li>{{ $accommodatie->min_personen }} - {{ $accommodatie->max_personen }} <span data-i18n="reserve.persons">personen</span></li>
                                        @if ($accommodatie->huisdieren_toegestaan)
                                            <li data-i18n="home.filter.pets_allowed">Huisdieren toegestaan</li>
                                        @else
                                            <li data-i18n="home.filter.pets_not_allowed">Huisdieren niet toegestaan</li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="text-lg font-semibold text-accent">&euro;{{ number_format($accommodatie->prijs_per_nacht, 2, ',', '.') }}</span>
                                        <span class="text-sm text-muted" data-i18n="home.accommodations.per_night">/ nacht</span>
                                    </div>
                                    <button type="button"
                                        onclick='openReserveerModal({{ $accommodatie->id }}, {!! json_encode($accommodatie->vertaaldeTitel($locale)) !!})'
                                        class="inline-flex items-center justify-center rounded-lg border border-border bg-white px-5 py-2.5 text-sm font-medium text-primary transition hover:border-accent hover:text-accent"
                                        data-i18n="home.accommodations.book">Reserveer
                                    </button>
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
        document.addEventListener('locale-changed', function() { location.reload(); });
    </script>
    <script>
        (function () {
            const personenSelect = document.getElementById('filter-personen');
            const typeSelect = document.getElementById('filter-type');
            const huisdierSelect = document.getElementById('filter-huisdieren');
            const aankomstInput = document.getElementById('filter-aankomst');
            const vertrekInput = document.getElementById('filter-vertrek');
            const dateError = document.getElementById('date-error');
            const filterMessage = document.getElementById('filter-message');
            const resultsWrapper = document.getElementById('results-wrapper');
            const resultsCount = document.getElementById('results-count');
            const noResults = document.getElementById('no-results');
            const cards = document.querySelectorAll('.accommodatie-card');
            const personenControl = document.getElementById('personen-control');
            const personenPanel = document.getElementById('personen-panel');
            const personenChevron = document.getElementById('personen-chevron');
            const personenSummary = document.getElementById('personen-summary');
            const personenClose = document.getElementById('personen-close');

            const counts = {
                adults: 0,
                children: 0,
                babies: 0,
            };

            function toDateValue(date) {
                return new Date(date + 'T00:00:00');
            }

            function formatDate(date) {
                return date.toISOString().slice(0, 10);
            }

            function addOneDay(dateValue) {
                const nextDay = new Date(dateValue.getTime());
                nextDay.setDate(nextDay.getDate() + 1);
                return nextDay;
            }

            function subtractOneDay(dateValue) {
                const previousDay = new Date(dateValue.getTime());
                previousDay.setDate(previousDay.getDate() - 1);
                return previousDay;
            }

            function syncDateLimits() {
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                aankomstInput.min = formatDate(today);
                aankomstInput.max = '';
                vertrekInput.min = formatDate(addOneDay(today));
                vertrekInput.max = '';

                if (aankomstInput.value) {
                    const arrivalDate = toDateValue(aankomstInput.value);
                    const nextDay = addOneDay(arrivalDate);
                    vertrekInput.min = formatDate(nextDay);

                    if (vertrekInput.value && toDateValue(vertrekInput.value) <= arrivalDate) {
                        vertrekInput.value = '';
                    }
                } else {
                    vertrekInput.min = formatDate(addOneDay(today));
                }

                if (vertrekInput.value) {
                    const departureDate = toDateValue(vertrekInput.value);
                    const lastArrivalDay = subtractOneDay(departureDate);
                    aankomstInput.max = formatDate(lastArrivalDay);

                    if (aankomstInput.value && toDateValue(aankomstInput.value) >= departureDate) {
                        aankomstInput.value = '';
                    }
                }
            }

            function validateDates() {
                syncDateLimits();

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const arrivalValue = aankomstInput.value ? toDateValue(aankomstInput.value) : null;
                const departureValue = vertrekInput.value ? toDateValue(vertrekInput.value) : null;

                let hasError = false;

                if (arrivalValue && arrivalValue < today) {
                    hasError = true;
                }

                if (departureValue && departureValue <= today) {
                    hasError = true;
                }

                if (arrivalValue && departureValue && departureValue <= arrivalValue) {
                    hasError = true;
                }

                dateError.classList.toggle('hidden', !hasError);

                return !hasError;
            }

            function getTotalPersons() {
                return counts.adults + counts.children + counts.babies;
            }

            function updatePersonenSummary() {
                const total = getTotalPersons();
                personenSelect.value = total;
                personenSummary.textContent = window.__('home.persons_' + (total === 1 ? 'one' : 'other'), { count: total });
            }

            function togglePanel(open) {
                personenPanel.classList.toggle('hidden', !open);
                personenChevron.classList.toggle('rotate-180', open);
            }

            function applyFilters() {
                if (!validateDates()) return;

                const selectedPersons = parseInt(personenSelect.value, 10) || 0;
                const selectedType = typeSelect.value;
                const selectedPets = huisdierSelect.value;

                let visibleCount = 0;

                cards.forEach((card) => {
                    const minPersons = parseInt(card.dataset.persons || '0', 10);
                    const matchPersons = !selectedPersons || selectedPersons >= minPersons;
                    const matchType = !selectedType || card.dataset.type === selectedType;
                    const matchPets = !selectedPets || card.dataset.pets === selectedPets;
                    const showCard = matchPersons && matchType && matchPets;

                    card.classList.toggle('hidden', !showCard);
                    if (showCard) visibleCount++;
                });

                filterMessage.classList.add('hidden');
                resultsWrapper.classList.remove('hidden');
                resultsCount.textContent = window.__('home.results_' + (visibleCount === 1 ? 'one' : 'other'), { count: visibleCount });
                noResults.classList.toggle('hidden', visibleCount > 0);
                noResults.textContent = visibleCount > 0 ? '' : window.__('home.no_results');
            }

            personenControl.addEventListener('click', function (e) {
                e.stopPropagation();
                togglePanel(personenPanel.classList.contains('hidden'));
            });

            personenClose.addEventListener('click', function () {
                togglePanel(false);
            });

            document.addEventListener('click', function (e) {
                if (!personenControl.contains(e.target) && !personenPanel.contains(e.target)) {
                    togglePanel(false);
                }
            });

            document.querySelectorAll('.personen-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const key = btn.dataset.key;
                    const isInc = btn.classList.contains('personen-increment');
                    const el = document.getElementById('count-' + key);

                    if (isInc) {
                        counts[key]++;
                    } else {
                        if (counts[key] <= 0) return;
                        counts[key]--;
                    }

                    el.textContent = counts[key];
                    updatePersonenSummary();
                    applyFilters();
                });
            });

            [typeSelect, huisdierSelect, aankomstInput, vertrekInput].forEach(el => {
                if (!el) return;
                el.addEventListener('change', applyFilters);
            });

            syncDateLimits();
            updatePersonenSummary();
            applyFilters();
        })();

        // --- Reserveer Modal ---
        function openReserveerModal(id, titel) {
            document.getElementById('modal-accommodatie-id').value = id;
            document.getElementById('modal-title').textContent = window.__('reserve.modal_title').replace('{name}', titel);
            document.getElementById('reserveer-modal').classList.remove('hidden');
            document.getElementById('reserveer-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.getElementById('reserveer-error').classList.add('hidden');
            document.getElementById('reserveer-error').textContent = '';

            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            var dayAfter = new Date(today);
            dayAfter.setDate(dayAfter.getDate() + 2);

            var modalAank = document.getElementById('aankomst-datum');
            var modalVert = document.getElementById('vertrek-datum');
            modalAank.min = tomorrow.toISOString().split('T')[0];
            modalVert.min = dayAfter.toISOString().split('T')[0];

            var filterAank = document.getElementById('filter-aankomst');
            var filterVert = document.getElementById('filter-vertrek');
            var filterAankVal = filterAank ? filterAank.value : '';
            var filterVertVal = filterVert ? filterVert.value : '';

            if (filterAankVal && filterVertVal) {
                modalAank.value = filterAankVal;
                modalVert.value = filterVertVal;
            } else {
                if (!modalAank.value) modalAank.value = tomorrow.toISOString().split('T')[0];
                if (!modalVert.value) modalVert.value = dayAfter.toISOString().split('T')[0];
            }
        }

        function closeReserveerModal() {
            document.getElementById('reserveer-modal').classList.add('hidden');
            document.getElementById('reserveer-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.reserveer-btn');
            if (btn) {
                openReserveerModal(btn.getAttribute('data-id'), btn.getAttribute('data-titel'));
            }
        });

        document.getElementById('reserveer-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeReserveerModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReserveerModal();
            }
        });
    </script>
@endsection