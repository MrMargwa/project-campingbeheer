@extends('layouts.app')

@section('title', 'Reserveren')

@push('styles')
    <style>
        #map .leaflet-div-icon {
            background: none !important;
            border: none !important;
        }

        .accommodation-marker {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            cursor: pointer;
        }

        .accommodation-marker:hover {
            transform: scale(1.3);
            z-index: 1000 !important;
        }

        .accommodation-marker.selected {
            transform: scale(1.3);
            z-index: 1000 !important;
        }

        .leaflet-tooltip {
            font-family: inherit !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
            padding: 6px 10px !important;
            border-radius: 6px !important;
            border: 1px solid #D1D9D4 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
        }

        .leaflet-tooltip-top:before {
            border-top-color: #D1D9D4 !important;
        }
    </style>
@endpush

@section('content')
    @include('partials.banner', [
        'title' => 'Reserveren',
        'image' => 'https://www.torentjeshoek.nl/images/dummy/E-veld_Torentjeshoek.JPG',
        'i18nKey' => 'reserve.title',
    ])

    <section class="flex flex-col lg:flex-row gap-8">
        <div class="flex-1 min-h-0">
            <div id="map" class="w-full rounded-xl border border-border shadow-sm bg-surface" style="height: 600px">
            </div>
        </div>

        <aside class="w-full lg:w-72 shrink-0" style="height: 600px">
            <div class="h-full bg-surface border border-border rounded-xl shadow-sm p-5 flex flex-col gap-5 overflow-y-auto">
                <h2 class="font-semibold text-primary text-sm uppercase tracking-wide">Filters</h2>

                {{-- Type filters --}}
                <fieldset>
                    <legend class="text-xs font-medium text-muted mb-2 uppercase tracking-wide">Type</legend>
                    <div class="space-y-2" id="type-filters">
                        @php
                            $resTypes = \App\Models\Accommodation::select('type', 'type_en', 'type_de', 'type_fy')
                                ->distinct('type')
                                ->get()
                                ->keyBy('type');
                        @endphp
                        @foreach ($resTypes as $typeKey => $typeRow)
                            @php $typeLabel = $typeRow->{'type_' . $locale} ?: $typeKey; @endphp
                            <label class="flex items-center gap-2.5 text-sm text-primary cursor-pointer select-none">
                                <input type="checkbox" value="{{ $typeKey }}" checked
                                    class="filter-type w-4 h-4 rounded border-border text-accent focus:ring-accent/30 cursor-pointer">
                                {{ $typeLabel }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Status filters --}}
                <fieldset>
                    <legend class="text-xs font-medium text-muted mb-2 uppercase tracking-wide">Status</legend>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2.5 text-sm text-primary cursor-pointer select-none">
                            <input type="checkbox" value="available" checked id="filter-available"
                                class="filter-status w-4 h-4 rounded border-border text-accent focus:ring-accent/30 cursor-pointer">
                            <span class="inline-block w-2.5 h-2.5 rounded-sm bg-[#2A6A4E]"></span>
                            <span data-i18n="reserve.available">Beschikbaar</span>
                        </label>
                        <label class="flex items-center gap-2.5 text-sm text-primary cursor-pointer select-none">
                            <input type="checkbox" value="unavailable" checked id="filter-unavailable"
                                class="filter-status w-4 h-4 rounded border-border text-accent focus:ring-accent/30 cursor-pointer">
                            <span class="inline-block w-2.5 h-2.5 rounded-sm bg-[#BD4C4C]"></span>
                            <span data-i18n="reserve.not_available">Niet beschikbaar</span>
                        </label>
                    </div>
                </fieldset>
            </div>
        </aside>
    </section>

    {{-- Legenda --}}
    @php
        $legendTypes = \App\Models\Accommodation::select('type', 'type_en', 'type_de', 'type_fy')
            ->distinct('type')
            ->get()
            ->keyBy('type');
        $legendColors = [
            'Chalet' => '#2A6A4E',
            'Blokhut' => '#8B4513',
            'Safaritent' => '#D97706',
            'Vakantiehuis' => '#7C3AED',
            'Camping' => '#2563EB',
        ];
    @endphp
    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-muted">
        <span class="font-medium text-primary">Legenda</span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded-sm" style="background:#2A6A4E"></span> Chalets
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded-sm" style="background:#8B4513"></span> Blokhutten
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded-sm" style="background:#D97706"></span> Safaritenten
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded-sm" style="background:#7C3AED"></span> Vakantiehuisjes
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded-sm" style="background:#2563EB"></span> Campings
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block w-3 h-3 rounded-sm" style="background:#6B7280"></span> Parkeerplaats
        </span>
    </div>

    {{-- Detail card --}}
    <div id="detail-card" class="hidden mt-8"></div>

    @include('partials.reserveer-modal')
@endsection

@section('scripts')
    <script>
        var POSTCODE_API_KEY = '{{ $postcodeApiKey }}';

        document.addEventListener('DOMContentLoaded', function() {
            (function() {
                'use strict';

                // --- Data ---
                const accommodations = @json($accommodations);
                const items = accommodations.filter(function(a) {
                    return a.latitude != null && a.longitude != null;
                });
                if (items.length === 0) return;

                // --- Map ---
                var map = L.map('map', {
                    center: [53.0968, 5.6878],
                    zoom: 17,
                    maxZoom: 19,
                    minZoom: 14,
                    zoomControl: true,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
                }).addTo(map);

                // --- GeoJSON polygons ---
                var areaColors = {
                    'Chalets': '#2A6A4E',
                    'Blokhutten': '#8B4513',
                    'Safaritenten': '#D97706',
                    'Vakantiehuisjes': '#7C3AED',
                    'Campings': '#2563EB',
                    'Parkeerplaats': '#6B7280',
                };

                fetch('/data.geojson')
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(geojson) {
                        L.geoJSON(geojson, {
                            filter: function(feature) {
                                return feature.geometry.type === 'Polygon';
                            },
                            style: function(feature) {
                                var name = feature.properties.name;
                                var color = areaColors[name] || '#647069';
                                return {
                                    color: color,
                                    weight: 2,
                                    opacity: 0.8,
                                    fillColor: color,
                                    fillOpacity: 0.12,
                                };
                            }
                        }).addTo(map);
                    });

                // --- State ---
                var selectedId = null;
                var selectedMarkerData = null;
                var detailCard = document.getElementById('detail-card');
                var markerDataList = [];

                // --- Helpers ---
                function isAvailable(acc) {
                    return acc.status === 'available';
                }

                function statusColor(acc) {
                    return isAvailable(acc) ? '#2A6A4E' : '#BD4C4C';
                }

                function createIcon(acc, selected) {
                    var color = statusColor(acc);
                    var size = selected ? 28 : 22;
                    var shadow = selected ?
                        '0 0 0 3px rgba(42,106,78,0.35), 0 3px 12px rgba(0,0,0,0.3)' :
                        '0 2px 6px rgba(0,0,0,0.25)';
                    var scale = selected ? 'scale(1.3)' : '';

                    return L.divIcon({
                        className: '',
                        html: '<div class="accommodation-marker' + (selected ? ' selected' : '') +
                            '" style="' +
                            'width:' + size + 'px;height:' + size + 'px;' +
                            'background:' + color + ';' +
                            'border:3px solid #fff;' +
                            'border-radius:4px;' +
                            'box-shadow:' + shadow + ';' +
                            'transform:' + scale + ';' +
                            '"></div>',
                        iconSize: [size + 6, size + 6],
                        iconAnchor: [(size + 6) / 2, (size + 6) / 2],
                    });
                }

                // --- Build markers ---
                items.forEach(function(acc) {
                    var icon = createIcon(acc, false);
                    var marker = L.marker([acc.latitude, acc.longitude], {
                        icon: icon
                    }).addTo(map);

                    marker.bindTooltip(
                        '<strong>' + esc(acc.title) + '</strong><br>' +
                        esc(acc.type) + ' &middot; ' +
                        (acc.price_per_night > 0 ? '&euro;' + parseFloat(acc.price_per_night)
                            .toFixed(2) + window.__('reserve.per_night') : '') +
                        '<br><span style="color:' + statusColor(acc) + ';font-weight:500">' +
                        (isAvailable(acc) ? 'Beschikbaar' : 'Niet beschikbaar') + '</span>', {
                            direction: 'top',
                            offset: [0, -10]
                        }
                    );

                    marker.on('click', function(e) {
                        L.DomEvent.stopPropagation(e);
                        selectAccommodation(acc, marker);
                    });

                    markerDataList.push({
                        marker: marker,
                        acc: acc,
                        visible: true
                    });
                });

                // --- Filtering ---
                function applyFilters() {
                    var activeTypes = [];
                    document.querySelectorAll('.filter-type:checked').forEach(function(cb) {
                        activeTypes.push(cb.value);
                    });

                    var activeStatuses = [];
                    document.querySelectorAll('.filter-status:checked').forEach(function(cb) {
                        activeStatuses.push(cb.value);
                    });

                    markerDataList.forEach(function(md) {
                        var typeMatch = activeTypes.indexOf(md.acc.type) !== -1;
                        var statusMatch = activeStatuses.indexOf(md.acc.status) !== -1;
                        var shouldShow = typeMatch && statusMatch;

                        if (shouldShow && !md.visible) {
                            map.addLayer(md.marker);
                            md.visible = true;
                        } else if (!shouldShow && md.visible) {
                            map.removeLayer(md.marker);
                            md.visible = false;
                        }
                    });

                    // Close detail if selected accommodation is now hidden
                    if (selectedId !== null) {
                        var stillVisible = markerDataList.some(function(md) {
                            return (md.acc.id || md.acc.title) === selectedId && md.visible;
                        });
                        if (!stillVisible) {
                            closeDetail();
                        }
                    }
                }

                document.querySelectorAll('.filter-type, .filter-status').forEach(function(cb) {
                    cb.addEventListener('change', applyFilters);
                });

                // --- Selection ---
                map.on('click', function() {
                    closeDetail();
                });

                function selectAccommodation(acc, marker) {
                    if (selectedId === (acc.id || acc.title)) return;

                    if (selectedMarkerData != null) {
                        var prev = selectedMarkerData;
                        prev.marker.setIcon(createIcon(prev.acc, false));
                        prev.marker.setZIndexOffset(0);
                    }

                    selectedId = acc.id || acc.title;
                    selectedMarkerData = {
                        marker: marker,
                        acc: acc
                    };

                    marker.setIcon(createIcon(acc, true));
                    marker.setZIndexOffset(1000);
                    marker.openTooltip();

                    showDetail(acc);
                }

                function closeDetail() {
                    if (selectedMarkerData != null) {
                        var prev = selectedMarkerData;
                        prev.marker.setIcon(createIcon(prev.acc, false));
                        prev.marker.setZIndexOffset(0);
                    }
                    selectedId = null;
                    selectedMarkerData = null;
                    detailCard.classList.add('hidden');
                }

                // --- Detail card ---
                var typeColors = {
                    'Chalet': '#2A6A4E',
                    'Blokhut': '#8B4513',
                    'Camperplaats': '#2563EB',
                    'Camping': '#2563EB',
                    'Safaritent': '#D97706',
                    'Vakantiehuis': '#7C3AED',
                    'Vakantiewoning': '#7C3AED',
                };

                function showDetail(acc) {
                    var free = isAvailable(acc);
                    var price = '&euro;' + parseFloat(acc.price_per_night).toFixed(2);
                    var accType = acc.type;
                    var typeColor = typeColors[acc.type] || '#647069';
                    var statusLabel = free ? 'Beschikbaar' : 'Niet beschikbaar';
                    var statusColorVal = free ? '#2A6A4E' : '#BD4C4C';
                    var accTitle = acc.title;
                    var accDesc = acc.description;

                    // Build image HTML with fallback
                    var imgHtml = '';
                    if (acc.image) {
                        imgHtml = '<img src="' + esc('/images/' + acc.image) + '" alt="' + esc(accTitle) +
                            '" class="w-full h-full object-cover" onerror="this.style.display=\'none\';this.nextSibling.style.display=\'flex\'"><div class="hidden w-full h-full items-center justify-center bg-secondary text-muted text-sm font-medium" style="display:none">' +
                            esc(accType) + '</div>';
                    } else {
                        imgHtml =
                            '<div class="w-full h-full flex items-center justify-center bg-secondary text-muted text-sm font-medium">' +
                            esc(accType) + '</div>';
                    }

                    detailCard.innerHTML = [
                        '<div class="bg-surface border border-border rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row">',
                        '<div class="md:w-[35%] h-52 md:h-auto bg-secondary overflow-hidden relative">',
                        imgHtml,
                        '</div>',
                        '<div class="flex-1 p-6 flex flex-col gap-3">',
                        '<div class="flex items-start justify-between gap-3">',
                        '<h3 class="font-semibold text-primary text-xl">' + esc(acc.title) + '</h3>',
                        '<button id="detail-close" class="bg-transparent border-0 cursor-pointer text-muted hover:text-primary transition text-xl leading-none shrink-0" aria-label="Sluiten">&times;</button>',
                        '</div>',
                        accType ?
                        '<span class="inline-block self-start px-2.5 py-0.5 rounded-full text-xs font-medium" style="background:' +
                        typeColor + '15;color:' + typeColor + ';text-transform:capitalize">' + esc(acc
                            .type) + '</span>' : '',
                        acc.description ? '<p class="text-sm text-muted leading-relaxed">' + esc(acc
                            .description) + '</p>' : '',
                        '<div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-muted">',
                        '<span>' + acc.min_persons + '-' + acc.max_persons + ' personen</span>',
                        acc.price_per_night > 0 ? '<span class="font-semibold" style="color:' + typeColor +
                        '">' + price + ' ' + window.__('reserve.per_night') + '</span>' : '',
                        '<span class="inline-flex items-center gap-1.5"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:' +
                        statusColorVal + '"></span>' + statusLabel + '</span>',
                        '</div>',
                        '<div class="mt-auto pt-3 border-t border-border flex justify-end">',
                        (free && acc.id) ? '<button type="button" data-id="' + acc.id +
                        '" data-title="' + esc(acc.title) +
                        '" class="reserveer-btn bg-accent hover:bg-accent-hover text-white font-medium px-6 py-2.5 rounded-lg transition text-sm border-0 cursor-pointer">Reserveer Nu</button>' :
                        '<span class="text-sm text-muted italic">Deze accommodatie is momenteel niet beschikbaar voor reservering.</span>',
                        '</div>',
                        '</div>',
                        '</div>',
                    ].join('');

                    detailCard.classList.remove('hidden');

                    document.getElementById('detail-close').addEventListener('click', function(e) {
                        e.stopPropagation();
                        closeDetail();
                    });

                    detailCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }

                // --- Fit bounds ---
                var group = L.featureGroup(
                    items.map(function(a) {
                        return L.marker([a.latitude, a.longitude]);
                    })
                );
                map.fitBounds(group.getBounds().pad(0.1));

                function esc(str) {
                    if (typeof str !== 'string') return str;
                    var d = document.createElement('div');
                    d.appendChild(document.createTextNode(str));
                    return d.innerHTML;
                }
            })();
        });

        // --- Reserveer Modal ---
        function openBookingModal(id, title) {
            document.getElementById('modal-accommodation-id').value = id;
            document.getElementById('modal-title').textContent = window.__('reserve.modal_title').replace('{name}', title);
            document.getElementById('booking-modal').classList.remove('hidden');
            document.getElementById('booking-modal').classList.add('flex');
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

        // Event delegation for reserveer buttons (created dynamically)
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.reserveer-btn');
            if (btn) {
                openBookingModal(btn.getAttribute('data-id'), btn.getAttribute('data-title'));
            }
        });

        // Close modal on backdrop click
        document.getElementById('booking-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
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
                        if (data.straat) {
                            document.getElementById('straat-input').value = data.straat;
                        }
                        if (data.plaats) {
                            document.getElementById('plaats-input').value = data.plaats;
                        }
                        if (data.land) {
                            document.querySelector('input[name="land"]').value = data.land;
                        }
                        document.getElementById('reserveer-error').classList.add('hidden');
                    } else {
                        showAddressError(window.__('reserve.form.address_not_found'));
                    }
                })
                .catch(function() {
                    showAddressError(window.__('reserve.form.address_fetch_error'));
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

        // Form submission
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
                        if (firstKey) {
                            msg = err.errors[firstKey][0];
                        }
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
    </script>
@endsection
