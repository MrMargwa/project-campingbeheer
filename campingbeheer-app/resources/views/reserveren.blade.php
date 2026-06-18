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

            </div>
        </aside>
    </section>



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
                window.ACCOMMODATIONS = accommodations;
                const items = accommodations.filter(function(a) {
                    return a.latitude != null && a.longitude != null && a.status === 'available';
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

                // --- Legend control (inside map) ---
                var legendControl = L.control({ position: 'bottomleft' });
                legendControl.onAdd = function() {
                    var div = L.DomUtil.create('div', 'rounded-lg bg-white/90 backdrop-blur-sm shadow-md border border-border px-3 py-2 text-xs');
                    div.innerHTML =
                        '<div class="font-medium text-primary mb-1 text-xs">Legenda</div>' +
                        '<div class="space-y-0.5">' +
                        '<span class="inline-flex items-center gap-1.5 mr-3"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#2A6A4E"></span> Chalets</span>' +
                        '<span class="inline-flex items-center gap-1.5 mr-3"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#8B4513"></span> Blokhutten</span>' +
                        '<span class="inline-flex items-center gap-1.5 mr-3"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#D97706"></span> Safaritenten</span>' +
                        '<span class="inline-flex items-center gap-1.5 mr-3"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#7C3AED"></span> Vakantiehuisjes</span>' +
                        '<span class="inline-flex items-center gap-1.5 mr-3"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#2563EB"></span> Camperplaatsen</span>' +
                        '<span class="inline-flex items-center gap-1.5"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#6B7280"></span> Parkeerplaats</span>' +
                        '</div>';
                    return div;
                };
                legendControl.addTo(map);

                // --- State ---
                var selectedId = null;
                var selectedMarkerData = null;
                var detailCard = document.getElementById('detail-card');
                var markerDataList = [];

                // --- Helpers ---
                function createIcon(acc, selected) {
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
                            'background:#2A6A4E;' +
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
                        esc(acc.type),
                        {
                            direction: 'top',
                            offset: [0, -10],
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

                // --- Filtering (type only) ---
                function applyFilters() {
                    var activeTypes = [];
                    document.querySelectorAll('.filter-type:checked').forEach(function(cb) {
                        activeTypes.push(cb.value);
                    });

                    markerDataList.forEach(function(md) {
                        var typeMatch = activeTypes.indexOf(md.acc.type) !== -1;

                        if (typeMatch && !md.visible) {
                            map.addLayer(md.marker);
                            md.visible = true;
                        } else if (!typeMatch && md.visible) {
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

                document.querySelectorAll('.filter-type').forEach(function(cb) {
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
                    'Camper': '#2563EB',
                    'Safaritent': '#D97706',
                    'Vakantiehuis': '#7C3AED',
                    'Vakantiewoning': '#7C3AED',
                };

                function showDetail(acc) {
                    var price = '&euro;' + parseFloat(acc.price_per_night).toFixed(2);
                    var accType = acc.type;
                    var typeColor = typeColors[acc.type] || '#647069';
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
                        '</div>',
                        '<div class="mt-auto pt-3 border-t border-border flex justify-end">',
                        acc.id ? '<button type="button" data-id="' + acc.id +
                        '" data-title="' + esc(acc.title) +
                        '" data-min-persons="' + (acc.min_persons || 1) +
                        '" data-max-persons="' + (acc.max_persons || 99) +
                        '" class="reserveer-btn bg-accent hover:bg-accent-hover text-white font-medium px-6 py-2.5 rounded-lg transition text-sm border-0 cursor-pointer">Reserveer Nu</button>' : '',
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
        function openBookingModal(id, title, minPersons, maxPersons) {
            document.getElementById('modal-accommodation-id').value = id;
            document.getElementById('modal-title').textContent = window.__('reserve.modal_title').replace('{name}', title);
            document.getElementById('booking-modal').classList.remove('hidden');
            document.getElementById('booking-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.getElementById('booking-error').classList.add('hidden');
            document.getElementById('booking-error').textContent = '';

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
                openBookingModal(
                    btn.getAttribute('data-id'),
                    btn.getAttribute('data-title'),
                    btn.getAttribute('data-min-persons'),
                    btn.getAttribute('data-max-persons')
                );
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

        // Postcode search and form submission are handled by address.js (loaded via app.js)

        // Auto-open modal if navigated from home page with accommodation ID
        (function() {
            var params = new URLSearchParams(window.location.search);
            var accId = params.get('accommodatie');
            if (accId && window.ACCOMMODATIONS) {
                var acc = window.ACCOMMODATIONS.find(function(a) { return String(a.id) === accId; });
                if (acc) {
                    openBookingModal(acc.id, acc.title);
                }
            }
        })();
    </script>
@endsection
