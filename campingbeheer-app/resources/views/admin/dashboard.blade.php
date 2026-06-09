@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <section class="p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-primary">Dashboard</h1>
            <p class="text-muted text-sm mt-1">Welkom bij het campingbeheer paneel.</p>
        </div>

        <div class="bg-surface border border-border rounded-xl p-6 max-w-2xl">
            <h2 class="text-lg font-semibold text-primary mb-4">Nieuwe Reservering</h2>
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-primary mb-1">Accommodatie</label>
                    <select id="admin-accommodatie-select"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                        <option value="">Selecteer een accommodatie...</option>
                        @foreach ($accommodaties as $acc)
                            <option value="{{ $acc->id }}" data-titel="{{ $acc->titel }}">{{ $acc->titel }}
                                ({{ $acc->type }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="admin-reserveer-btn" disabled
                    class="bg-accent text-white font-medium px-6 py-2.5 rounded-lg transition text-sm border-0 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Reserveer Nu
                </button>
            </div>
        </div>
    </section>

    @include('partials.reserveer-modal', ['showSearch' => true])
@endsection

@section('scripts')
    <script>
        var POSTCODE_API_KEY = '{{ $postcodeApiKey }}';

        document.addEventListener('DOMContentLoaded', function() {
            var select = document.getElementById('admin-accommodatie-select');
            var btn = document.getElementById('admin-reserveer-btn');

            select.addEventListener('change', function() {
                btn.disabled = !this.value;
            });

            btn.addEventListener('click', function() {
                if (!select.value) return;
                var option = select.options[select.selectedIndex];
                openReserveerModal(option.value, option.dataset.titel);
            });

            initGastSearch();
        });

        function openReserveerModal(id, titel) {
            document.getElementById('modal-accommodatie-id').value = id;
            document.getElementById('modal-title').textContent = 'Reserveren: ' + titel;
            document.getElementById('reserveer-modal').classList.remove('hidden');
            document.getElementById('reserveer-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.getElementById('reserveer-error').classList.add('hidden');
            document.getElementById('reserveer-error').textContent = '';
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
                                    '<div class="px-3 py-2 text-muted">Geen gasten gevonden</div>';
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
            btn.textContent = 'Zoeken...';

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
                    btn.textContent = 'Zoeken';
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
            submitBtn.textContent = 'Bezig...';
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
                        alert(data.message || 'Reservering succesvol!');
                        form.reset();
                        document.getElementById('admin-accommodatie-select').value = '';
                        document.getElementById('admin-reserveer-btn').disabled = true;
                    }
                })
                .catch(function(err) {
                    var msg = 'Er is een fout opgetreden. Probeer opnieuw.';
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
                    submitBtn.textContent = 'Reservering Bevestigen';
                });
        });

        function esc(str) {
            if (typeof str !== 'string') return '';
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }
    </script>
@endsection
