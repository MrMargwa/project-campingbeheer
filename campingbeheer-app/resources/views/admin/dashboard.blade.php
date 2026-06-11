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
                        @forelse ($vandaagAankomst as $boeking)
                            <tr>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">{{ $boeking->naam }}</td>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">
                                    @if ($boeking->accommodatie)
                                        {{ $boeking->accommodatie->titel }}
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
                        @forelse ($vandaagVertrek as $boeking)
                            <tr>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">{{ $boeking->naam }}</td>
                                <td class="border border-border px-4 py-2 text-primary" style="text-align:center;background:#FFF">
                                    @if ($boeking->accommodatie)
                                        {{ $boeking->accommodatie->titel }}
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
                        @forelse ($boekingen as $boeking)
                            <tr class="border-b border-border last:border-0 bg-surface">
                                <td class="px-4 py-3 font-medium" style="text-align:center;color:#000;background:#FFF">{{ $boeking->naam }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">
                                    @if ($boeking->accommodatie)
                                        {{ $boeking->accommodatie->titel }}
                                    @else
                                        <span data-i18n="admin.dashboard.unknown">Onbekend</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">{{ \Carbon\Carbon::parse($boeking->aankomst_datum)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">{{ \Carbon\Carbon::parse($boeking->vertrek_datum)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">{{ $boeking->aantal_personen }}</td>
                                <td class="px-4 py-3" style="text-align:center;color:#000;background:#FFF">&euro; {{ number_format($boeking->accommodatie?->prijs_per_nacht ?? $boeking->totaal_prijs, 2, ',', '.') }}</td>
                                <td class="px-4 py-3" style="text-align:center;background:#FFF">
                                    @if ($boeking->status === 'in_afwachting')
                                        <div class="flex items-center gap-3" style="justify-content:center">
                                            <form action="{{ route('admin.reserveringen.approve', $boeking) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-800 text-lg leading-none bg-transparent border-0 cursor-pointer p-0"
                                                    title="Goedkeuren" data-i18n-title="admin.reserveringen.approve">&check;</button>
                                            </form>
                                            <button type="button"
                                                onclick="openRejectModal({{ $boeking->id }}, '{{ addslashes($boeking->naam) }}')"
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
            @if ($boekingen->hasPages())
                <div class="border-t border-border px-5 py-3">
                    <div class="flex items-center justify-center gap-1">
                        @if ($boekingen->onFirstPage())
                            <span class="px-2 py-1 text-xs text-muted">&lsaquo;</span>
                        @else
                            <a href="{{ $boekingen->previousPageUrl() }}" class="px-2 py-1 text-xs text-primary hover:text-accent">&lsaquo;</a>
                        @endif
                        @foreach ($boekingen->getUrlRange(1, $boekingen->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="px-2 py-1 text-xs rounded {{ $page === $boekingen->currentPage() ? 'bg-accent text-white' : 'text-primary hover:text-accent' }}">{{ $page }}</a>
                        @endforeach
                        @if ($boekingen->hasMorePages())
                            <a href="{{ $boekingen->nextPageUrl() }}" class="px-2 py-1 text-xs text-primary hover:text-accent">&rsaquo;</a>
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
                    <textarea name="afkeur_reden" rows="3"
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
                openReserveerModal();
            });

            initGastSearch();
        });

    function openReserveerModal(id, titel) {
        var modal = document.getElementById('reserveer-modal');
        var titleEl = document.getElementById('modal-title');
        var accSelect = document.getElementById('modal-accommodatie-select');
        var accHidden = document.getElementById('modal-accommodatie-id');

        if (id) {
            if (accSelect) accSelect.value = id;
            if (accHidden) accHidden.value = id;
            titleEl.textContent = window.__('reserve.modal_title', {name: titel});
        } else {
            if (accSelect) accSelect.value = '';
            if (accHidden) accHidden.value = '';
            titleEl.textContent = 'Zelf boeking toevoegen';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.getElementById('reserveer-error').classList.add('hidden');
        document.getElementById('reserveer-error').textContent = '';

        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        var dayAfter = new Date(today);
        dayAfter.setDate(dayAfter.getDate() + 2);

        var aankomstInput = document.getElementById('aankomst-datum');
        var vertrekInput = document.getElementById('vertrek-datum');
        aankomstInput.min = tomorrow.toISOString().split('T')[0];
        vertrekInput.min = dayAfter.toISOString().split('T')[0];
        if (aankomstInput && !aankomstInput.value) {
            aankomstInput.value = tomorrow.toISOString().split('T')[0];
        }
        if (vertrekInput && !vertrekInput.value) {
            vertrekInput.value = dayAfter.toISOString().split('T')[0];
        }
    }

        function closeReserveerModal() {
            document.getElementById('reserveer-modal').classList.add('hidden');
            document.getElementById('reserveer-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e) {
            if (e.target === document.getElementById('reserveer-modal')) {
                closeReserveerModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReserveerModal();
            }
        });

        function initGastSearch() {
            var searchInput = document.getElementById('gast-search');
            if (!searchInput) return;

            var resultsContainer = document.getElementById('gast-search-results');
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
                    fetch('/admin/zoek-gasten?q=' + encodeURIComponent(query))
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
                            data.forEach(function(gast) {
                                html +=
                                    '<div class="gast-result px-3 py-2 cursor-pointer hover:bg-secondary border-b border-border last:border-0" data-naam="' +
                                    esc(gast.naam) + '" data-email="' + esc(gast.email) +
                                    '" data-telefoon="' + esc(gast.telefoon) +
                                    '" data-postcode="' + esc(gast.postcode) +
                                    '" data-huisnummer="' + esc(gast.huisnummer) +
                                    '" data-straat="' + esc(gast.straat) + '" data-plaats="' +
                                    esc(gast.plaats) + '" data-land="' + esc(gast.land) + '">';
                                html += '<div class="font-medium text-primary">' + esc(gast
                                    .naam) + '</div>';
                                if (gast.email) {
                                    html += '<div class="text-xs text-muted">' + esc(gast
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
                var result = e.target.closest('.gast-result');
                if (!result) return;

                document.querySelector('input[name="naam"]').value = result.dataset.naam;
                document.querySelector('input[name="email"]').value = result.dataset.email || '';
                document.querySelector('input[name="telefoon"]').value = result.dataset.telefoon || '';
                document.querySelector('input[name="postcode"]').value = result.dataset.postcode || '';
                document.querySelector('input[name="huisnummer"]').value = result.dataset.huisnummer || '';
                document.querySelector('input[name="straat"]').value = result.dataset.straat || '';
                document.querySelector('input[name="plaats"]').value = result.dataset.plaats || '';
                document.querySelector('input[name="land"]').value = result.dataset.land || 'Nederland';

                resultsContainer.classList.add('hidden');
                resultsContainer.innerHTML = '';
                searchInput.value = '';
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('#gast-search, #gast-search-results')) {
                    resultsContainer.classList.add('hidden');
                }
            });
        }

        document.getElementById('postcode-input')?.addEventListener('input', function() {
            var btn = document.getElementById('postcode-zoeken');
            var val = this.value.trim().replace(/\s+/g, '');
            btn.disabled = val.length < 4;
            if (val.length >= 4) {
                btn.classList.remove('cursor-not-allowed', 'opacity-50');
            } else {
                btn.classList.add('cursor-not-allowed', 'opacity-50');
            }
        });

        document.getElementById('postcode-zoeken')?.addEventListener('click', function() {
            var postcode = document.getElementById('postcode-input').value.trim();
            var huisnummer = document.getElementById('huisnummer-input').value.trim();
            if (!postcode) return;

            var btn = this;
            btn.disabled = true;
            btn.textContent = window.__('reserve.form.searching');

            fetchAddressByPostcode(postcode, huisnummer)
                .then(function(data) {
                    if (data) {
                        if (data.straat) document.getElementById('straat-input').value = data.straat;
                        if (data.plaats) document.getElementById('plaats-input').value = data.plaats;
                        if (data.land) document.querySelector('input[name="land"]').value = data.land;
                        document.getElementById('reserveer-error').classList.add('hidden');
                    } else {
                        showAddressError('Adres niet gevonden voor deze postcode.');
                    }
                })
                .catch(function() {
                    showAddressError('Kon adres niet ophalen. Vul handmatig in.');
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.textContent = window.__('reserve.form.search');
                });
        });

        function fetchAddressByPostcode(postcode, huisnummer) {
            var normalized = postcode.replace(/\s+/g, '').toUpperCase();

            if (!POSTCODE_API_KEY) {
                return fallbackFetchAddress(normalized, huisnummer);
            }

            var url = 'https://postcode.tech/api/v1/postcode' +
                '?postcode=' + encodeURIComponent(normalized) +
                '&number=' + encodeURIComponent(huisnummer || '');

            return fetch(url, {
                    headers: {
                        'Authorization': 'Bearer ' + POSTCODE_API_KEY
                    }
                })
                .then(function(r) {
                    if (!r.ok) throw new Error();
                    return r.json();
                })
                .then(function(json) {
                    return {
                        straat: json.street || json.straatnaam || '',
                        plaats: json.city || json.woonplaats || '',
                        land: 'Nederland'
                    };
                })
                .catch(function() {
                    return fallbackFetchAddress(normalized, huisnummer);
                });
        }

        function fallbackFetchAddress(normalized, huisnummer) {
            function tryPDOK() {
                var fqParts = ['postcode:' + encodeURIComponent(normalized)];
                if (huisnummer) fqParts.push('huisnummer:' + encodeURIComponent(huisnummer));
                var url = 'https://geodata.nationaalgeoregister.nl/locatieserver/v3/free' +
                    '?q=*:*&rows=1&fq=' + fqParts.join('&fq=');
                return fetch(url).then(function(r) {
                        if (!r.ok) throw new Error();
                        return r.json();
                    })
                    .then(function(json) {
                        var doc = json.response?.docs?.[0];
                        if (!doc) throw new Error();
                        return {
                            straat: doc.straatnaam || '',
                            plaats: doc.woonplaatsnaam || doc.city || '',
                            land: 'Nederland'
                        };
                    });
            }

            function tryNominatim() {
                var query = normalized;
                if (huisnummer) query += '+' + huisnummer;
                var url = 'https://nominatim.openstreetmap.org/search' +
                    '?q=' + encodeURIComponent(query) +
                    '&format=json&addressdetails=1&countrycodes=nl&limit=1';
                return fetch(url, {
                        headers: {
                            'User-Agent': 'Campingbeheer-App/1.0'
                        }
                    })
                    .then(function(r) {
                        if (!r.ok) throw new Error();
                        return r.json();
                    })
                    .then(function(json) {
                        if (!json || json.length === 0) throw new Error();
                        var addr = json[0].address || {};
                        return {
                            straat: addr.road || addr.street || '',
                            plaats: addr.city || addr.town || addr.village || addr.place || '',
                            land: addr.country || 'Nederland'
                        };
                    });
            }

            function tryZippopotam() {
                var url = 'https://api.zippopotam.us/NL/' + encodeURIComponent(normalized);
                return fetch(url).then(function(r) {
                        if (!r.ok) throw new Error();
                        return r.json();
                    })
                    .then(function(json) {
                        var place = json.places?.[0];
                        if (!place) return null;
                        return {
                            straat: '',
                            plaats: place['place name'] || place.city || '',
                            land: json.country || 'Netherlands'
                        };
                    });
            }

            return tryPDOK().catch(tryNominatim).catch(tryZippopotam).catch(function() {
                return null;
            });
        }

        function showAddressError(msg) {
            var el = document.getElementById('reserveer-error');
            el.textContent = msg;
            el.classList.remove('hidden');
        }

        document.getElementById('reserveer-form')?.addEventListener('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);
            var submitBtn = document.getElementById('reserveer-submit');
            var errorEl = document.getElementById('reserveer-error');

            submitBtn.disabled = true;
            submitBtn.textContent = window.__('reserve.form.confirming');
            errorEl.classList.add('hidden');

            fetch('/reserveren', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(function(r) {
                    if (!r.ok) {
                        return r.json().then(function(err) {
                            throw err;
                        });
                    }
                    return r.json();
                })
                .then(function(data) {
                    if (data.success) {
                        closeReserveerModal();
                        alert(data.message || window.__('reserve.form.success'));
                        form.reset();
                    }
                })
                .catch(function(err) {
                    var msg = window.__('reserve.form.generic_error');
                    if (err.errors) {
                        var firstKey = Object.keys(err.errors)[0];
                        if (firstKey) msg = err.errors[firstKey][0];
                    } else if (err.message) {
                        msg = err.message;
                    }
                    errorEl.textContent = msg;
                    errorEl.classList.remove('hidden');
                })
                .finally(function() {
                    submitBtn.disabled = false;
                    submitBtn.textContent = window.__('reserve.form.confirm');
                });
        });

        function esc(str) {
            if (typeof str !== 'string') return '';
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }

        function openRejectModal(id, naam) {
            document.getElementById('reject-form').action = '/admin/reserveringen/' + id + '/afkeuren';
            document.getElementById('reject-guest-name').textContent = window.__('admin.dashboard.confirm_reject', {name: naam});
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
