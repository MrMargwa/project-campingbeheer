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
                                class="absolute left-0 right-0 z-10 mt-2 hidden rounded-lg border border-border bg-white p-5 shadow-lg">
                                <div class="space-y-4">
                                    <h4 class="text-lg font-semibold text-primary" data-i18n="home.filter.travel_party">
                                        Reisgezelschap</h4>

                                    <div class="flex items-center justify-between gap-2">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.adults_label">Personen v.a 14 jaar
                                            </div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.adults_desc">Aantal
                                                volwassenen</div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button type="button" data-key="adults"
                                                class="persons-btn persons-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-adults" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="adults"
                                                class="persons-btn persons-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.children_label">Kinderen 3 t/m 13
                                                jaar</div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.children_desc">Kinderen
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button type="button" data-key="children"
                                                class="persons-btn persons-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-children" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="children"
                                                class="persons-btn persons-increment rounded-full border border-border px-3 py-1 text-sm leading-none">+</button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <div>
                                            <div class="text-sm" data-i18n="home.filter.babies_label">Baby t/m 2 jaar</div>
                                            <div class="text-xs text-muted" data-i18n="home.filter.babies_desc">Baby's</div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button type="button" data-key="babies"
                                                class="persons-btn persons-decrement rounded-full border border-border px-3 py-1 text-sm leading-none">-</button>
                                            <span id="count-babies" class="w-6 text-center text-sm tabular-nums">0</span>
                                            <button type="button" data-key="babies"
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
                                @foreach ($types as $typeKey => $typeRow)
                                    <option value="{{ $typeKey }}">{{ $typeRow->{'type_' . $locale} ?: $typeKey }}
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
                            <label for="filter-pets" class="mb-1 block text-xs font-medium text-primary"
                                data-i18n="home.filter.features">Kenmerken</label>
                            <select id="filter-pets"
                                class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="">Alle</option>
                                <option value="true" data-i18n="home.filter.pets_allowed">Huisdieren toegestaan</option>
                                <option value="false" data-i18n="home.filter.pets_not_allowed">Huisdieren niet toegestaan</option>
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
                @foreach ($accommodaties as $accommodation)
                    <article
                        class="accommodation-card overflow-hidden rounded-3xl border border-border bg-surface shadow-sm"
                        data-type="{{ $accommodation->type }}" data-persons="{{ $accommodation->min_persons }}"
                        data-max-persons="{{ $accommodation->max_persons }}"
                        data-huisdieren="{{ $accommodation->huisdieren_toegestaan ? 'true' : 'false' }}"
                        data-features="{{ $accommodation->features->pluck('name')->implode(' ') }}"
                        data-features-translated="{{ $accommodation->features->map(fn($k) => $k->translatedName($locale))->implode(' ') }}"
                        data-id="{{ $accommodation->id }}">
                        <div class="grid lg:grid-cols-2">
                            <div class="relative min-h-64 bg-secondary sm:min-h-72 lg:min-h-full">
                                @if ($accommodation->image && file_exists(public_path('images/' . $accommodation->image)))
                                    <img src="{{ asset('images/' . $accommodation->image) }}"
                                        alt="{{ $accommodation->translatedTitle($locale) }}"
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
                                        {{ $accommodation->translatedTitle($locale) }}</h3>
                                    <p class="text-sm text-muted">{{ $accommodation->translatedDescription($locale) }}</p>
                                    <ul class="space-y-2 text-sm text-muted">
                                        <li>{{ $accommodation->min_persons }} - {{ $accommodation->max_persons }} <span
                                                data-i18n="reserve.persons">personen</span></li>
                                        @if ($accommodation->features->isNotEmpty())
                                            @foreach ($accommodation->features as $feature)
                                                <li>{{ $feature->translatedName($locale) }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="text-lg font-semibold text-accent">&euro;{{ number_format($accommodation->price_per_night, 2, ',', '.') }}</span>
                                        <span class="text-sm text-muted" data-i18n="home.accommodations.per_night">/
                                            nacht</span>
                                    </div>
                                    <button type="button"
                                        class="reserveer-btn inline-flex items-center justify-center rounded-lg border border-border bg-white px-5 py-2.5 text-sm font-medium text-primary transition hover:border-accent hover:text-accent cursor-pointer"
                                        data-id="{{ $accommodation->id }}"
                                        data-title="{{ $accommodation->translatedTitle($locale) }}"
                                        data-min-persons="{{ $accommodation->min_persons }}"
                                        data-max-persons="{{ $accommodation->max_persons }}"
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

    {{-- Success popup --}}
    <div id="booking-success-popup"
        class="fixed inset-0 z-[10001] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-accent/10">
                <svg class="h-8 w-8 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-primary mb-2" data-i18n="reserve.form.success_title">Reservering gelukt!
            </h3>
            <p class="text-sm text-muted mb-6" data-i18n="reserve.form.success_message">Uw reservering is succesvol
                aangemaakt en moet nog worden goedgekeurd door de beheerder.</p>
            <button type="button" onclick="closeSuccessPopup()"
                class="bg-accent hover:bg-accent-hover text-white font-medium px-6 py-2.5 rounded-lg transition text-sm border-0 cursor-pointer"
                data-i18n="reserve.form.success_close">Sluiten</button>
        </div>
    </div>

    <script>
        function closeSuccessPopup() {
            document.getElementById('booking-success-popup').classList.add('hidden');
            document.getElementById('booking-success-popup').classList.remove('flex');
            document.body.style.overflow = '';
            // Remove query param from URL
            var url = new URL(window.location);
            url.searchParams.delete('booking');
            window.history.replaceState({}, '', url);
        }

        (function() {
            var params = new URLSearchParams(window.location.search);
            if (params.get('booking') === 'success') {
                var popup = document.getElementById('booking-success-popup');
                if (popup) {
                    popup.classList.remove('hidden');
                    popup.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
            }
        })();
    </script>
@endsection

@section('scripts')
    <script>
        document.addEventListener('locale-changed', function() {
            location.reload();
        });
    </script>
    <script>
        function openBookingModal(id, title, minPersons, maxPersons) {
            document.getElementById('modal-accommodation-id').value = id;
            document.getElementById('modal-title').textContent = window.__('reserve.modal_title') ? window.__('reserve.modal_title').replace('{name}', title) : 'Reserveren - ' + title;
            document.getElementById('booking-modal').classList.remove('hidden');
            document.getElementById('booking-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            var err = document.getElementById('booking-error');
            if (err) { err.classList.add('hidden'); err.textContent = ''; }

            var personsInput = document.getElementById('number-of-guests');
            var personsHint = document.getElementById('persons-range-hint');
            if (personsInput && minPersons && maxPersons) {
                personsInput.min = minPersons;
                personsInput.max = maxPersons;
                personsInput.value = minPersons;
                if (personsHint) {
                    personsHint.textContent = window.__('reserve.form.persons_range', {min: minPersons, max: maxPersons}) || '(min. ' + minPersons + ', max. ' + maxPersons + ')';
                    personsHint.classList.remove('hidden');
                }
            }

            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            var dayAfter = new Date(today);
            dayAfter.setDate(dayAfter.getDate() + 2);

            var arrivalInput = document.getElementById('arrival-date');
            var departureInput = document.getElementById('departure-date');
            if (arrivalInput) {
                arrivalInput.min = tomorrow.toISOString().split('T')[0];
                if (!arrivalInput.value) arrivalInput.value = tomorrow.toISOString().split('T')[0];
            }
            if (departureInput) {
                departureInput.min = dayAfter.toISOString().split('T')[0];
                if (!departureInput.value) departureInput.value = dayAfter.toISOString().split('T')[0];
            }
        }

        function closeBookingModal() {
            document.getElementById('booking-modal').classList.add('hidden');
            document.getElementById('booking-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.reserveer-btn');
            if (btn) {
                openBookingModal(
                    btn.getAttribute('data-id'),
                    btn.getAttribute('data-title'),
                    btn.getAttribute('data-min-persons'),
                    btn.getAttribute('data-max-persons')
                );
            }
        });

        document.getElementById('booking-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeBookingModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeBookingModal();
        });
    </script>
    <script>
        (function() {
            const personsSelect = document.getElementById('filter-personen');
            const typeSelect = document.getElementById('filter-type');
            const petsSelect = document.getElementById('filter-pets');
            const arrivalInput = document.getElementById('filter-arrival');
            const departureInput = document.getElementById('filter-departure');
            const dateError = document.getElementById('date-error');
            const filterMessage = document.getElementById('filter-message');
            const resultsWrapper = document.getElementById('results-wrapper');
            const resultsCount = document.getElementById('results-count');
            const noResults = document.getElementById('no-results');
            const cards = document.querySelectorAll('.accommodation-card');
            const personsControl = document.getElementById('persons-control');
            const personsPanel = document.getElementById('persons-panel');
            const personsChevron = document.getElementById('persons-chevron');
            const personsSummary = document.getElementById('persons-summary');
            const personsClose = document.getElementById('persons-close');

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

                arrivalInput.min = formatDate(today);
                arrivalInput.max = '';
                departureInput.min = formatDate(addOneDay(today));
                departureInput.max = '';

                if (arrivalInput.value) {
                    const arrivalDate = toDateValue(arrivalInput.value);
                    const nextDay = addOneDay(arrivalDate);
                    departureInput.min = formatDate(nextDay);

                    if (departureInput.value && toDateValue(departureInput.value) <= arrivalDate) {
                        departureInput.value = '';
                    }
                } else {
                    departureInput.min = formatDate(addOneDay(today));
                }

                if (departureInput.value) {
                    const departureDate = toDateValue(departureInput.value);
                    const lastArrivalDay = subtractOneDay(departureDate);
                    arrivalInput.max = formatDate(lastArrivalDay);

                    if (arrivalInput.value && toDateValue(arrivalInput.value) >= departureDate) {
                        arrivalInput.value = '';
                    }
                }
            }

            function validateDates() {
                syncDateLimits();

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const arrivalValue = arrivalInput.value ? toDateValue(arrivalInput.value) : null;
                const departureValue = departureInput.value ? toDateValue(departureInput.value) : null;

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

            function updatePersonsSummary() {
                const total = getTotalPersons();
                personsSelect.value = total;
                personsSummary.textContent = window.__('home.persons_' + (total === 1 ? 'one' : 'other'), {
                    count: total
                });
            }

            function togglePanel(open) {
                personsPanel.classList.toggle('hidden', !open);
                personsChevron.classList.toggle('rotate-180', open);
            }

            function applyFilters() {
                if (!validateDates()) return;

                const selectedPersons = parseInt(personsSelect.value, 10) || 0;
                const selectedType = typeSelect.value;
                const selectedPets = petsSelect.value;

                let visibleCount = 0;

                cards.forEach((card) => {
                    const minPersons = parseInt(card.dataset.persons || '0', 10);
                    const maxPersons = parseInt(card.dataset.maxPersons || '99', 10);
                    const matchPersons = !selectedPersons || (selectedPersons >= minPersons && selectedPersons <= maxPersons);
                    const matchType = !selectedType || card.dataset.type === selectedType;
                    const matchPets = !selectedPets || card.dataset.huisdieren === selectedPets;
                    const showCard = matchPersons && matchType && matchPets;

                    card.classList.toggle('hidden', !showCard);
                    if (showCard) visibleCount++;
                });

                filterMessage.classList.add('hidden');
                resultsWrapper.classList.remove('hidden');
                resultsCount.textContent = window.__('home.results_' + (visibleCount === 1 ? 'one' : 'other'), {
                    count: visibleCount
                });
                noResults.classList.toggle('hidden', visibleCount > 0);
                noResults.textContent = visibleCount > 0 ? '' : window.__('home.no_results');
            }

            personsControl.addEventListener('click', function(e) {
                e.stopPropagation();
                togglePanel(personsPanel.classList.contains('hidden'));
            });

            personsClose.addEventListener('click', function() {
                togglePanel(false);
            });

            document.addEventListener('click', function(e) {
                if (!personsControl.contains(e.target) && !personsPanel.contains(e.target)) {
                    togglePanel(false);
                }
            });

            document.querySelectorAll('.persons-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const key = btn.dataset.key;
                    const isInc = btn.classList.contains('persons-increment');
                    const el = document.getElementById('count-' + key);

                    if (isInc) {
                        counts[key]++;
                    } else {
                        if (counts[key] <= 0) return;
                        counts[key]--;
                    }

                    el.textContent = counts[key];
                    updatePersonsSummary();
                    applyFilters();
                });
            });

            [typeSelect, petsSelect, arrivalInput, departureInput].forEach(el => {
                if (!el) return;
                el.addEventListener('change', applyFilters);
            });

            syncDateLimits();
            updatePersonsSummary();
            applyFilters();
        })();
    </script>
@endsection
