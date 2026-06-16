@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <section class="p-8">
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 flex items-center justify-end gap-4">
            <button type="button" id="admin-reserveer-btn"
                class="bg-accent text-white font-medium px-5 py-2 rounded-lg transition text-sm border-0 cursor-pointer whitespace-nowrap">
                <span data-i18n="admin.dashboard.new_reservation">Zelf boeking toevoegen</span>
            </button>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <h2 class="text-base font-semibold mb-2" style="text-align:center;color:#000" data-i18n="admin.dashboard.today_arrival">Vandaag aankomst</h2>
                <table class="w-full text-sm border-collapse border border-border">
                    <thead>
                        <tr class="bg-secondary">
                            <th class="border border-border px-4 py-1.5 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.name">Naam</th>
                            <th class="border border-border px-4 py-1.5 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.verblijf">Verblijf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($todayArrivals as $booking)
                            <tr>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">{{ $booking->name }}</td>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">
                                    @if ($booking->accommodation)
                                        {{ $booking->accommodation->title }}
                                    @else
                                        <span data-i18n="admin.dashboard.unknown">Onbekend</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="border border-border px-4 py-4" style="text-align:center;color:#647069" data-i18n="admin.dashboard.arrivals_empty">Geen aankomsten vandaag.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <h2 class="text-base font-semibold mb-2" style="text-align:center;color:#000" data-i18n="admin.dashboard.today_departure">Vandaag vertrek</h2>
                <table class="w-full text-sm border-collapse border border-border">
                    <thead>
                        <tr class="bg-secondary">
                            <th class="border border-border px-4 py-1.5 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.name">Naam</th>
                            <th class="border border-border px-4 py-1.5 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.verblijf">Verblijf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($todayDepartures as $booking)
                            <tr>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">{{ $booking->name }}</td>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">
                                    @if ($booking->accommodation)
                                        {{ $booking->accommodation->title }}
                                    @else
                                        <span data-i18n="admin.dashboard.unknown">Onbekend</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="border border-border px-4 py-4" style="text-align:center;color:#647069" data-i18n="admin.dashboard.departures_empty">Geen vertrek vandaag.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border border-border bg-surface shadow-sm">
            <div class="border-b border-border px-5 py-3">
                <h2 class="text-base font-semibold" style="color:#000" data-i18n="admin.dashboard.all_requests">Alle aanvragen</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-secondary" style="text-align:center">
                            <th class="px-4 py-3 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.name">Naam</th>
                            <th class="px-4 py-3 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.verblijf">Verblijf</th>
                            <th class="px-4 py-3 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.arrival_date">Aankomst</th>
                            <th class="px-4 py-3 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.departure_date">Vertrek</th>
                            <th class="px-4 py-3 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.persons">Personen</th>
                            <th class="px-4 py-3 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.price_pp">Prijs P.P.</th>
                            <th class="px-4 py-3 text-xs font-medium" style="text-align:center;color:#000" data-i18n="admin.dashboard.actions">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr class="border-b border-border last:border-0 bg-surface">
                                <td class="px-4 py-3 font-medium" style="text-align:center;color:#000;background:#FFF">{{ $booking->name }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">
                                    @if ($booking->accommodation)
                                        {{ $booking->accommodation->title }}
                                    @else
                                        <span data-i18n="admin.dashboard.unknown">Onbekend</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">{{ \Carbon\Carbon::parse($booking->arrival_date)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">{{ \Carbon\Carbon::parse($booking->departure_date)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">{{ $booking->number_of_persons }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">&euro; {{ number_format($booking->accommodation?->price_per_night ?? $booking->total_price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3" style="text-align:center;background:#FFF">
                                    @if ($booking->status === 'pending')
                                        <div class="flex items-center gap-3" style="justify-content:center">
                                            <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-800 text-lg leading-none bg-transparent border-0 cursor-pointer p-0"
                                                    title="Goedkeuren" data-i18n-title="admin.reserveringen.approve">&check;</button>
                                            </form>
                                            <button type="button"
                                                onclick="openRejectModal({{ $booking->id }}, '{{ addslashes($booking->name) }}')"
                                                class="text-red-600 hover:text-red-800 text-lg leading-none bg-transparent border-0 cursor-pointer p-0"
                                                title="Afkeuren" data-i18n-title="admin.reserveringen.reject">&times;</button>
                                        </div>
                                    @else
                                        <span class="text-xs" style="color:#647069">--</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8" style="text-align:center;color:#647069" data-i18n="admin.dashboard.no_requests">Geen aanvragen gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($bookings->hasPages())
                <div class="border-t border-border px-5 py-3">
                    <div class="flex items-center justify-center gap-1">
                        @if ($bookings->onFirstPage())
                            <span class="px-2 py-1 text-xs text-muted">&lsaquo;</span>
                        @else
                            <a href="{{ $bookings->previousPageUrl() }}" class="px-2 py-1 text-xs text-primary hover:text-accent">&lsaquo;</a>
                        @endif
                        @foreach ($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="px-2 py-1 text-xs rounded {{ $page === $bookings->currentPage() ? 'bg-accent text-white' : 'text-primary hover:text-accent' }}">{{ $page }}</a>
                        @endforeach
                        @if ($bookings->hasMorePages())
                            <a href="{{ $bookings->nextPageUrl() }}" class="px-2 py-1 text-xs text-primary hover:text-accent">&rsaquo;</a>
                        @else
                            <span class="px-2 py-1 text-xs text-muted">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Afkeur Modal --}}
    <div id="reject-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="border-b border-border px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-lg font-semibold text-primary" data-i18n="admin.reserveringen.reject_title">Reservering afkeuren</h3>
                <button type="button" onclick="closeRejectModal()"
                    class="text-muted hover:text-primary text-2xl leading-none bg-transparent border-0 cursor-pointer">&times;</button>
            </div>
            <form id="reject-form" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-sm text-primary" id="reject-guest-name"></p>
                <div>
                    <label class="block text-sm font-medium text-primary mb-1" data-i18n="admin.reserveringen.reject_reason">Reden van afkeuring (optioneel)</label>
                    <textarea name="rejection_reason" rows="3"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()"
                        class="rounded-lg border border-border px-4 py-2 text-sm font-medium text-primary hover:bg-secondary border-0 cursor-pointer" data-i18n="admin.reserveringen.cancel">Annuleren</button>
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 border-0 cursor-pointer" data-i18n="admin.reserveringen.confirm_reject">Afkeuren</button>
                </div>
            </form>
        </div>
    </div>

    @include('partials.reserveer-modal', ['showSearch' => true])
@endsection

@section('scripts')
    <script>
        var POSTCODE_API_KEY = '{{ $postcodeApiKey }}';

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('admin-reserveer-btn').addEventListener('click', function() {
                openBookingModal();
            });

            initGuestSearch();
        });

    function openBookingModal(id, title) {
        var modal = document.getElementById('booking-modal');
        var titleEl = document.getElementById('modal-title');
        var accSelect = document.getElementById('modal-accommodation-select');
        var accHidden = document.getElementById('modal-accommodation-id');

        if (id) {
            if (accSelect) accSelect.value = id;
            if (accHidden) accHidden.value = id;
            titleEl.textContent = window.__('reserve.modal_title', {name: title});
        } else {
            if (accSelect) accSelect.value = '';
            if (accHidden) accHidden.value = '';
            titleEl.textContent = 'Zelf boeking toevoegen';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.getElementById('booking-error').classList.add('hidden');
        document.getElementById('booking-error').textContent = '';

        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        var dayAfter = new Date(today);
        dayAfter.setDate(dayAfter.getDate() + 2);

        var arrivalInput = document.getElementById('arrival-date');
        var departureInput = document.getElementById('departure-date');
        arrivalInput.min = tomorrow.toISOString().split('T')[0];
        departureInput.min = dayAfter.toISOString().split('T')[0];
        if (arrivalInput && !arrivalInput.value) {
            arrivalInput.value = tomorrow.toISOString().split('T')[0];
        }
        if (departureInput && !departureInput.value) {
            departureInput.value = dayAfter.toISOString().split('T')[0];
        }
    }

        function closeBookingModal() {
            document.getElementById('booking-modal').classList.add('hidden');
            document.getElementById('booking-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e) {
            if (e.target === document.getElementById('booking-modal')) {
                closeBookingModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
            }
        });

        function initGuestSearch() {
            var searchInput = document.getElementById('guest-search');
            if (!searchInput) return;

            var resultsContainer = document.getElementById('guest-search-results');
            var debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                var query = this.value.trim();

                if (query.length < 2) {
                    resultsContainer.classList.add('hidden');
                    resultsContainer.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(function() {
                    fetch('/admin/search-guests?q=' + encodeURIComponent(query))
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(data) {
                            if (data.length === 0) {
                                resultsContainer.innerHTML =
                                    '<div class="px-3 py-2 text-muted">' + window.__('admin.dashboard.no_guests_found') + '</div>';
                                resultsContainer.classList.remove('hidden');
                                return;
                            }

                            var html = '';
                            data.forEach(function(guest) {
                                html +=
                                    '<div class="guest-result px-3 py-2 cursor-pointer hover:bg-secondary border-b border-border last:border-0" data-name="' +
                                    esc(guest.name) + '" data-email="' + esc(guest.email) +
                                    '" data-phone="' + esc(guest.phone) +
                                    '" data-postal-code="' + esc(guest.postal_code) +
                                    '" data-house-number="' + esc(guest.house_number) +
                                    '" data-street="' + esc(guest.street) + '" data-city="' +
                                    esc(guest.city) + '" data-country="' + esc(guest.country) + '">';
                                html += '<div class="font-medium text-primary">' + esc(guest
                                    .name) + '</div>';
                                if (guest.email) {
                                    html += '<div class="text-xs text-muted">' + esc(guest
                                        .email) + '</div>';
                                }
                                html += '</div>';
                            });
                            resultsContainer.innerHTML = html;
                            resultsContainer.classList.remove('hidden');
                        });
                }, 300);
            });

            resultsContainer.addEventListener('click', function(e) {
                var result = e.target.closest('.guest-result');
                if (!result) return;

                document.querySelector('input[name="name"]').value = result.dataset.name;
                document.querySelector('input[name="email"]').value = result.dataset.email || '';
                document.querySelector('input[name="phone"]').value = result.dataset.phone || '';
                document.querySelector('input[name="postal_code"]').value = result.dataset.postal_code || '';
                document.querySelector('input[name="house_number"]').value = result.dataset.house_number || '';
                document.querySelector('input[name="street"]').value = result.dataset.street || '';
                document.querySelector('input[name="city"]').value = result.dataset.city || '';
                document.querySelector('input[name="country"]').value = result.dataset.country || 'Nederland';

                resultsContainer.classList.add('hidden');
                resultsContainer.innerHTML = '';
                searchInput.value = '';
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('#guest-search, #guest-search-results')) {
                    resultsContainer.classList.add('hidden');
                }
            });
        }

        // Auto-initialized via address.js module

        function esc(str) {
            if (typeof str !== 'string') return '';
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }

        function openRejectModal(id, name) {
            document.getElementById('reject-form').action = '/admin/bookings/' + id + '/reject';
            document.getElementById('reject-guest-name').textContent = window.__('admin.dashboard.confirm_reject', {name: name});
            document.getElementById('reject-modal').classList.remove('hidden');
            document.getElementById('reject-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.getElementById('reject-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e) {
            if (e.target === document.getElementById('reject-modal')) {
                closeRejectModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
@endsection
