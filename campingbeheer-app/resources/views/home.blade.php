@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="space-y-10">
    <div class="relative overflow-visible rounded-3xl border border-border bg-surface shadow-sm">
        <div class="relative h-56 bg-secondary bg-cover bg-center md:h-72 lg:h-80"
            style="background-image: url('{{ asset('images/camping.png') }}');">
            <div class="absolute inset-0 bg-primary/15"></div>
        </div>

        <div class="relative -mt-12 px-4 pb-4 sm:px-6 lg:px-10">
            <div class="mx-auto max-w-5xl rounded-[1.75rem] border border-border bg-surface p-4 shadow-lg sm:p-5">
                <div class="grid gap-3 md:grid-cols-5 md:gap-2">
                    <div class="relative">
                        <label class="mb-1 block text-xs font-medium text-primary">Reisgezelschap</label>

                        <input type="hidden" id="filter-personen" value="2">

                        <button id="personen-control" type="button"
                            class="w-full rounded-lg border border-border bg-white px-3 py-3 text-left text-sm text-primary flex items-center justify-between">
                            <div>
                                <div class="text-sm">Reisgezelschap</div>
                                <div id="personen-summary" class="text-base font-medium">2 personen</div>
                            </div>
                            <svg id="personen-chevron" class="h-4 w-4 text-primary" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <div id="personen-panel"
                            class="absolute left-0 right-0 z-10 mt-2 hidden rounded-lg border border-border bg-white p-4 shadow-lg">
                            <div class="space-y-3">
                                <h4 class="text-lg font-semibold text-primary">Reisgezelschap</h4>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm">Personen v.a 14 jaar</div>
                                        <div class="text-xs text-muted">Aantal volwassenen</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-target="adults"
                                            class="personen-decrement rounded-full border border-border px-3 py-1 text-sm">-</button>
                                        <div id="count-adults" class="w-6 text-center">2</div>
                                        <button type="button" data-target="adults"
                                            class="personen-increment rounded-full border border-border px-3 py-1 text-sm">+</button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm">Kinderen 3 t/m 13 jaar</div>
                                        <div class="text-xs text-muted">Kinderen</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-target="children"
                                            class="personen-decrement rounded-full border border-border px-3 py-1 text-sm">-</button>
                                        <div id="count-children" class="w-6 text-center">0</div>
                                        <button type="button" data-target="children"
                                            class="personen-increment rounded-full border border-border px-3 py-1 text-sm">+</button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm">Baby t/m 2 jaar</div>
                                        <div class="text-xs text-muted">Baby's</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-target="babies"
                                            class="personen-decrement rounded-full border border-border px-3 py-1 text-sm">-</button>
                                        <div id="count-babies" class="w-6 text-center">0</div>
                                        <button type="button" data-target="babies"
                                            class="personen-increment rounded-full border border-border px-3 py-1 text-sm">+</button>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button id="personen-close" type="button"
                                        class="rounded-lg bg-surface border border-border px-3 py-1 text-sm">Gereed</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="filter-type" class="mb-1 block text-xs font-medium text-primary">Soort verblijf</label>
                        <select id="filter-type"
                            class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                            <option value="">Alle soorten</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-aankomst" class="mb-1 block text-xs font-medium text-primary">Aankomst</label>
                        <input id="filter-aankomst" type="date" min="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                    </div>

                    <div>
                        <label for="filter-vertrek" class="mb-1 block text-xs font-medium text-primary">Vertrek</label>
                        <input id="filter-vertrek" type="date" min="{{ now()->addDay()->format('Y-m-d') }}"
                            class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                    </div>

                    <div>
                        <label for="filter-kenmerken" class="mb-1 block text-xs font-medium text-primary">Kenmerken</label>
                        <select id="filter-kenmerken"
                            class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                            <option value="">Niets geselecteerd</option>
                            @foreach ($kenmerken as $kenmerk)
                                <option value="{{ $kenmerk->naam }}">{{ $kenmerk->naam }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p id="date-error"
        class="hidden rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
        Aankomst en vertrek mogen niet in het verleden liggen en vertrek moet minstens één dag na aankomst zijn.
    </p>

    <div id="filter-message" class="rounded-3xl border border-border bg-surface p-5 shadow-sm sm:p-6">
        <p class="text-sm font-medium text-muted">U moet eerst nog filteren.</p>
        <p class="mt-2 text-lg font-semibold text-primary">Kies reisgezelschap, soort verblijf of kenmerken om de verblijven te zien.</p>
    </div>

    <section id="results-wrapper" class="hidden space-y-5">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-primary sm:text-2xl">Verblijven</h2>
            <span id="results-count" class="text-sm text-muted"></span>
        </div>

        <div id="no-results"
            class="hidden rounded-2xl border border-border bg-white px-4 py-3 text-sm text-muted">
            Geen verblijven gevonden met deze filters.
        </div>

        <div id="results-list" class="space-y-6">
            @foreach ($accommodaties as $accommodatie)
                <article class="accommodatie-card overflow-hidden rounded-3xl border border-border bg-surface shadow-sm"
                    data-type="{{ $accommodatie->type }}"
                    data-persons="{{ $accommodatie->min_personen }}"
                    data-max-persons="{{ $accommodatie->max_personen }}"
                    data-features="{{ $accommodatie->kenmerken->pluck('naam')->implode(' ') }}"
                    data-id="{{ $accommodatie->id }}">
                    <div class="grid lg:grid-cols-2">
                        <div class="relative min-h-64 bg-secondary sm:min-h-72 lg:min-h-full">
                            @if ($accommodatie->afbeelding && file_exists(public_path('images/' . $accommodatie->afbeelding)))
                                <img src="{{ asset('images/' . $accommodatie->afbeelding) }}" alt="{{ $accommodatie->titel }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <span class="absolute left-4 top-4 h-px w-[85%] rotate-48 origin-top-left bg-primary/80"></span>
                                <span class="absolute left-4 top-4 h-px w-[85%] -rotate-48 origin-top-left bg-primary/80"></span>
                            @endif
                        </div>

                        <div class="flex flex-col justify-between p-6 sm:p-8">
                            <div class="space-y-4">
                                <h3 class="text-2xl font-semibold text-primary">{{ $accommodatie->titel }}</h3>
                                <p class="text-sm text-muted">{{ $accommodatie->beschrijving }}</p>
                                <ul class="space-y-2 text-sm text-muted">
                                    <li>{{ $accommodatie->min_personen }} - {{ $accommodatie->max_personen }} personen</li>
                                    @if ($accommodatie->kenmerken->isNotEmpty())
                                        @foreach ($accommodatie->kenmerken as $kenmerk)
                                            <li>{{ $kenmerk->naam }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>

                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <span class="text-lg font-semibold text-accent">&euro;{{ number_format($accommodatie->prijs_per_nacht, 2, ',', '.') }}</span>
                                    <span class="text-sm text-muted">/ nacht</span>
                                </div>
                                <a href="{{ route('reserveren') }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-border bg-white px-5 py-2.5 text-sm font-medium text-primary transition hover:border-accent hover:text-accent">
                                    Reserveer
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</section>
@endsection

@section('scripts')
<script>
    (function () {
        const typeSelect = document.getElementById('filter-type');
        const kenmerkenSelect = document.getElementById('filter-kenmerken');
        const aankomstInput = document.getElementById('filter-aankomst');
        const vertrekInput = document.getElementById('filter-vertrek');
        const dateError = document.getElementById('date-error');
        const filterMessage = document.getElementById('filter-message');
        const resultsWrapper = document.getElementById('results-wrapper');
        const resultsCount = document.getElementById('results-count');
        const noResults = document.getElementById('no-results');
        const cards = document.querySelectorAll('.accommodatie-card');

        const personenSelect = document.getElementById('filter-personen');
        const personenControl = document.getElementById('personen-control');
        const personenPanel = document.getElementById('personen-panel');
        const personenSummary = document.getElementById('personen-summary');
        const countAdults = document.getElementById('count-adults');
        const countChildren = document.getElementById('count-children');
        const countBabies = document.getElementById('count-babies');

        let adults = parseInt(personenSelect.value || '2', 10) || 2;
        let children = 0;
        let babies = 0;

        function updateSummary() {
            const total = adults + children + babies;
            personenSummary.textContent = `${total} ${total === 1 ? 'persoon' : 'personen'}`;
            personenSelect.value = total;
        }

        function refreshCounts() {
            countAdults.textContent = adults;
            countChildren.textContent = children;
            countBabies.textContent = babies;
            updateSummary();
        }

        function closePanel() { personenPanel.classList.add('hidden'); }
        function openPanel() { personenPanel.classList.remove('hidden'); }

        personenControl.addEventListener('click', function (e) {
            e.stopPropagation();
            personenPanel.classList.toggle('hidden');
        });

        document.querySelectorAll('.personen-increment').forEach(btn => {
            btn.addEventListener('click', () => {
                const t = btn.getAttribute('data-target');
                if (t === 'adults') adults = Math.min(12, adults + 1);
                if (t === 'children') children = Math.min(12, children + 1);
                if (t === 'babies') babies = Math.min(12, babies + 1);
                refreshCounts();
                applyFilters();
            });
        });
        document.querySelectorAll('.personen-decrement').forEach(btn => {
            btn.addEventListener('click', () => {
                const t = btn.getAttribute('data-target');
                if (t === 'adults') adults = Math.max(1, adults - 1);
                if (t === 'children') children = Math.max(0, children - 1);
                if (t === 'babies') babies = Math.max(0, babies - 1);
                refreshCounts();
                applyFilters();
            });
        });

        document.getElementById('personen-close').addEventListener('click', () => closePanel());
        document.addEventListener('click', (e) => {
            if (!personenPanel.classList.contains('hidden')) {
                if (!personenPanel.contains(e.target) && !personenControl.contains(e.target)) closePanel();
            }
        });

        refreshCounts();

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

        function applyFilters() {
            if (!validateDates()) return;

            const selectedPersons = personenSelect ? parseInt(personenSelect.value, 10) : 0;
            const selectedType = typeSelect.value;
            const selectedFeature = kenmerkenSelect.value;

            let visibleCount = 0;

            cards.forEach((card) => {
                const minPersons = parseInt(card.dataset.persons || '0', 10);
                const matchPersons = !selectedPersons || selectedPersons >= minPersons;
                const matchType = !selectedType || card.dataset.type === selectedType;
                const matchFeature = !selectedFeature || (card.dataset.features || '').toLowerCase().includes(selectedFeature.toLowerCase());
                const showCard = matchPersons && matchType && matchFeature;

                card.classList.toggle('hidden', !showCard);
                if (showCard) visibleCount++;
            });

            filterMessage.classList.add('hidden');
            resultsWrapper.classList.remove('hidden');
            resultsCount.textContent = visibleCount === 1 ? '1 verblijf gevonden' :
                `${visibleCount} verblijven gevonden`;
            noResults.classList.toggle('hidden', visibleCount > 0);
            noResults.textContent = visibleCount > 0 ? '' : 'Geen verblijven gevonden met deze filters.';
        }

        [personenSelect, typeSelect, kenmerkenSelect, aankomstInput, vertrekInput].forEach(el => {
            if (!el) return;
            el.addEventListener('change', applyFilters);
        });
        syncDateLimits();
    })();
</script>
@endsection
