@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="space-y-10">
        <div class="relative overflow-hidden rounded-3xl border border-border bg-surface shadow-sm">
            <div class="relative h-56 bg-secondary bg-cover bg-center md:h-72 lg:h-80"
                style="background-image: url('{{ asset('images/camping.png') }}');">
                <div class="absolute inset-0 bg-primary/15"></div>
            </div>

            <div class="relative -mt-12 px-4 pb-4 sm:px-6 lg:px-10">
                <div class="mx-auto max-w-5xl rounded-[1.75rem] border border-border bg-surface p-4 shadow-lg sm:p-5">
                    <div class="grid gap-3 md:grid-cols-5 md:gap-2">
                        <div>
                            <label for="filter-personen" class="mb-1 block text-xs font-medium text-primary">Reisgezelschap</label>
                            <select id="filter-personen" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="">2 personen</option>
                                <option value="3">3 personen</option>
                                <option value="4">4 personen</option>
                                <option value="5">5 personen</option>
                                <option value="6">6 personen</option>
                                <option value="7">7 personen</option>
                                <option value="8">8 personen</option>
                                <option value="9">9 personen</option>
                                <option value="10">10 personen</option>
                                <option value="11">11 personen</option>
                                <option value="12">12 personen</option>
                            </select>
                        </div>

                        <div>
                            <label for="filter-type" class="mb-1 block text-xs font-medium text-primary">Soort verblijf</label>
                            <select id="filter-type" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="">Alle soorten</option>
                                <option value="chalet">Chalet</option>
                                <option value="lodge">Lodge</option>
                            </select>
                        </div>

                        <div>
                            <label for="filter-aankomst" class="mb-1 block text-xs font-medium text-primary">Aankomst</label>
                            <input id="filter-aankomst" type="date" min="{{ now()->format('Y-m-d') }}" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                        </div>

                        <div>
                            <label for="filter-vertrek" class="mb-1 block text-xs font-medium text-primary">Vertrek</label>
                            <input id="filter-vertrek" type="date" min="{{ now()->addDay()->format('Y-m-d') }}" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                        </div>

                        <div>
                            <label for="filter-kenmerken" class="mb-1 block text-xs font-medium text-primary">Kenmerken</label>
                            <select id="filter-kenmerken" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                                <option value="">Niets geselecteerd</option>
                                <option value="wifi">WiFi</option>
                                <option value="sanitair">Eigen sanitair</option>
                                <option value="huisdieren">Huisdieren toegestaan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p id="date-error" class="hidden rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
            Aankomst en vertrek mogen niet in het verleden liggen en vertrek moet minstens één dag na aankomst zijn.
        </p>

        <div id="filter-message" class="rounded-3xl border border-border bg-surface p-5 shadow-sm sm:p-6">
            <p class="text-sm font-medium text-muted">U moet eerst nog filteren.</p>
            <p class="mt-2 text-lg font-semibold text-primary">Kies reisgezelschap, soort verblijf of kenmerken om de chalets te zien.</p>
        </div>

        <section id="results-wrapper" class="hidden space-y-5">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-primary sm:text-2xl">Chalets</h2>
                <span id="results-count" class="text-sm text-muted"></span>
            </div>

            <div id="no-results" class="hidden rounded-2xl border border-border bg-white px-4 py-3 text-sm text-muted">Geen chalets gevonden met deze filters.</div>

            <div id="results-list" class="space-y-6">
                <article class="chalet-card overflow-hidden rounded-3xl border border-border bg-surface shadow-sm" data-type="chalet" data-persons="2" data-features="wifi sanitair">
                    <div class="grid lg:grid-cols-2">
                        <div class="relative min-h-64 bg-secondary sm:min-h-72 lg:min-h-full">
                            <span class="absolute left-4 top-4 h-px w-[85%] rotate-48 origin-top-left bg-primary/80"></span>
                            <span class="absolute left-4 top-4 h-px w-[85%] -rotate-48 origin-top-left bg-primary/80"></span>
                        </div>

                        <div class="flex flex-col justify-between p-6 sm:p-8">
                            <div class="space-y-4">
                                <h3 class="text-2xl font-semibold text-primary">Chalet 1</h3>
                                <ul class="space-y-2 text-sm text-muted">
                                    <li>• Compleet ingerichte Limburgse chalets</li>
                                    <li>• 2 slaapkamers</li>
                                    <li>• Eigen douche, keuken en toilet</li>
                                    <li>• Gratis WiFi</li>
                                    <li>• Huisdieren niet toegestaan</li>
                                </ul>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('reserveren') }}" class="inline-flex items-center justify-center rounded-lg border border-border bg-white px-5 py-2.5 text-sm font-medium text-primary transition hover:border-accent hover:text-accent">Reserveer</a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </section>
@endsection

@section('scripts')
    <script>
        (function() {
            const personenSelect = document.getElementById('filter-personen');
            const typeSelect = document.getElementById('filter-type');
            const kenmerkenSelect = document.getElementById('filter-kenmerken');
            const aankomstInput = document.getElementById('filter-aankomst');
            const vertrekInput = document.getElementById('filter-vertrek');
            const dateError = document.getElementById('date-error');
            const filterMessage = document.getElementById('filter-message');
            const resultsWrapper = document.getElementById('results-wrapper');
            const resultsCount = document.getElementById('results-count');
            const noResults = document.getElementById('no-results');
            const cards = document.querySelectorAll('.chalet-card');

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

                const selectedPersons = personenSelect ? personenSelect.value : '';
                const selectedType = typeSelect.value;
                const selectedFeature = kenmerkenSelect.value;

                let visibleCount = 0;

                cards.forEach((card) => {
                    const minPersons = parseInt(card.dataset.persons || '0', 10);
                    const matchPersons = !selectedPersons || parseInt(selectedPersons, 10) >= minPersons;
                    const matchType = !selectedType || card.dataset.type === selectedType;
                    const matchFeature = !selectedFeature || card.dataset.features.includes(selectedFeature);
                    const showCard = matchPersons && matchType && matchFeature;

                    card.classList.toggle('hidden', !showCard);
                    if (showCard) visibleCount++;
                });

                filterMessage.classList.add('hidden');
                resultsWrapper.classList.remove('hidden');
                resultsCount.textContent = visibleCount === 1 ? '1 chalet gevonden' :
                `${visibleCount} chalets gevonden`;
                noResults.classList.toggle('hidden', visibleCount > 0);
                noResults.textContent = visibleCount > 0 ? '' : 'Geen chalets gevonden met deze filters.';
            }

            [personenSelect, typeSelect, kenmerkenSelect, aankomstInput, vertrekInput].forEach(el => {
                if (!el) return;
                el.addEventListener('change', applyFilters);
            });
            syncDateLimits();
        })();
    </script>
@endsection
