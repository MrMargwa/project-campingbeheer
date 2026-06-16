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
                            $resTypes = \App\Models\Accommodatie::select('type', 'type_en', 'type_de', 'type_fy')
                                ->distinct('type')
                                ->get()
                                ->keyBy('type');
                        @endphp
                        @foreach ($resTypes as $typeSleutel => $typeRij)
                            @php $typeLabel = $typeRij->{'type_' . $taal} ?: $typeSleutel; @endphp
                            <label class="flex items-center gap-2.5 text-sm text-primary cursor-pointer select-none">
                                <input type="checkbox" value="{{ $typeSleutel }}" checked
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
        $legendeTypes = \App\Models\Accommodatie::select('type', 'type_en', 'type_de', 'type_fy')
            ->distinct('type')
            ->get()
            ->keyBy('type');
        $legendeKleuren = [
            'Chalet' => '#2A6A4E',
            'Blokhut' => '#8B4513',
            'Safaritent' => '#D97706',
            'Vakantiehuis' => '#7C3AED',
            'Camper' => '#2563EB',
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
            <span class="inline-block w-3 h-3 rounded-sm" style="background:#2563EB"></span> Camperplaatsen
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
        var POSTCODE_API_SLEUTEL = '{{ $postcodeApiSleutel }}';

        document.addEventListener('DOMContentLoaded', function() {
            (function() {
                'use strict';

                // --- Data ---
                const accommodaties = @json($accommodaties);
                const items = accommodaties.filter(function(a) {
                    return a.latitude != null && a.longitude != null;
                });
                if (items.length === 0) return;

                // --- Map ---
                var kaart = L.map('map', {
                    center: [53.0968, 5.6878],
                    zoom: 17,
                    maxZoom: 19,
                    minZoom: 14,
                    zoomControl: true,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
                }).addTo(kaart);

                // --- GeoJSON polygons ---
                var gebiedKleuren = {
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
                                var color = gebiedKleuren[name] || '#647069';
                                return {
                                    color: color,
                                    weight: 2,
                                    opacity: 0.8,
                                    fillColor: color,
                                    fillOpacity: 0.12,
                                };
                            }
                        }).addTo(kaart);
                    });

                // --- State ---
                var geselecteerdId = null;
                var geselecteerdMarkerData = null;
                var detailKaart = document.getElementById('detail-card');
                var markerDataLijst = [];

                // --- Helpers ---
                function isBeschikbaar(acc) {
                    return acc.status === 'available';
                }

                function statusKleur(acc) {
                    return isBeschikbaar(acc) ? '#2A6A4E' : '#BD4C4C';
                }

                function maakIcoon(acc, selected) {
                    var color = statusKleur(acc);
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
                    var icoon = maakIcoon(acc, false);
                    var marker = L.marker([acc.latitude, acc.longitude], {
                        icon: icoon
                    }).addTo(kaart);

                    marker.bindTooltip(
                        '<strong>' + esc(acc.title) + '</strong><br>' +
                        esc(acc.type) + ' &middot; ' +
                        (acc.price_per_night > 0 ? '&euro;' + parseFloat(acc.price_per_night)
                            .toFixed(2) + window.__('reserve.per_night') : '') +
                        '<br><span style="color:' + statusKleur(acc) + ';font-weight:500">' +
                        (isBeschikbaar(acc) ? 'Beschikbaar' : 'Niet beschikbaar') + '</span>', {
                            direction: 'top',
                            offset: [0, -10]
                        }
                    );

                    marker.on('click', function(e) {
                        L.DomEvent.stopPropagation(e);
                        selecteerAccommodatie(acc, marker);
                    });

                    markerDataLijst.push({
                        marker: marker,
                        acc: acc,
                        visible: true
                    });
                });

                // --- Filtering ---
                function pasFiltersToe() {
                    var actieveTypes = [];
                    document.querySelectorAll('.filter-type:checked').forEach(function(cb) {
                        actieveTypes.push(cb.value);
                    });

                    var actieveStatussen = [];
                    document.querySelectorAll('.filter-status:checked').forEach(function(cb) {
                        actieveStatussen.push(cb.value);
                    });

                    markerDataLijst.forEach(function(md) {
                        var typeMatch = actieveTypes.indexOf(md.acc.type) !== -1;
                        var statusMatch = actieveStatussen.indexOf(md.acc.status) !== -1;
                        var moetTonen = typeMatch && statusMatch;

                        if (moetTonen && !md.visible) {
                            kaart.addLayer(md.marker);
                            md.visible = true;
                        } else if (!moetTonen && md.visible) {
                            kaart.removeLayer(md.marker);
                            md.visible = false;
                        }
                    });

                    // Close detail if selected accommodation is now hidden
                    if (geselecteerdId !== null) {
                        var nogZichtbaar = markerDataLijst.some(function(md) {
                            return (md.acc.id || md.acc.title) === geselecteerdId && md.visible;
                        });
                        if (!nogZichtbaar) {
                            sluitDetail();
                        }
                    }
                }

                document.querySelectorAll('.filter-type, .filter-status').forEach(function(cb) {
                    cb.addEventListener('change', pasFiltersToe);
                });

                // --- Selection ---
                kaart.on('click', function() {
                    sluitDetail();
                });

                function selecteerAccommodatie(acc, marker) {
                    if (geselecteerdId === (acc.id || acc.title)) return;

                    if (geselecteerdMarkerData != null) {
                        var vorige = geselecteerdMarkerData;
                        vorige.marker.setIcon(maakIcoon(vorige.acc, false));
                        vorige.marker.setZIndexOffset(0);
                    }

                    geselecteerdId = acc.id || acc.title;
                    geselecteerdMarkerData = {
                        marker: marker,
                        acc: acc
                    };

                    marker.setIcon(maakIcoon(acc, true));
                    marker.setZIndexOffset(1000);
                    marker.openTooltip();

                    toonDetail(acc);
                }

                function sluitDetail() {
                    if (geselecteerdMarkerData != null) {
                        var vorige = geselecteerdMarkerData;
                        vorige.marker.setIcon(maakIcoon(vorige.acc, false));
                        vorige.marker.setZIndexOffset(0);
                    }
                    geselecteerdId = null;
                    geselecteerdMarkerData = null;
                    detailKaart.classList.add('hidden');
                }

                // --- Detail card ---
                var typeKleurs = {
                    'Chalet': '#2A6A4E',
                    'Blokhut': '#8B4513',
                    'Camperplaats': '#2563EB',
                    'Camper': '#2563EB',
                    'Safaritent': '#D97706',
                    'Vakantiehuis': '#7C3AED',
                    'Vakantiewoning': '#7C3AED',
                };

                function toonDetail(acc) {
                    var vrij = isBeschikbaar(acc);
                    var prijs = '&euro;' + parseFloat(acc.price_per_night).toFixed(2);
                    var accType = acc.type;
                    var typeKleur = typeKleurs[acc.type] || '#647069';
                    var statusLabel = vrij ? 'Beschikbaar' : 'Niet beschikbaar';
                    var statusKleurWaarde = vrij ? '#2A6A4E' : '#BD4C4C';
                    var accTitel = acc.title;
                    var accBeschrijving = acc.description;

                    // Build image HTML with fallback
                    var imgHtml = '';
                    if (acc.image) {
                        imgHtml = '<img src="' + esc('/images/' + acc.image) + '" alt="' + esc(accTitel) +
                            '" class="w-full h-full object-cover" onerror="this.style.display=\'none\';this.nextSibling.style.display=\'flex\'"><div class="hidden w-full h-full items-center justify-center bg-secondary text-muted text-sm font-medium" style="display:none">' +
                            esc(accType) + '</div>';
                    } else {
                        imgHtml =
                            '<div class="w-full h-full flex items-center justify-center bg-secondary text-muted text-sm font-medium">' +
                            esc(accType) + '</div>';
                    }

                    detailKaart.innerHTML = [
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
                        typeKleur + '15;color:' + typeKleur + ';text-transform:capitalize">' + esc(acc
                            .type) + '</span>' : '',
                        acc.description ? '<p class="text-sm text-muted leading-relaxed">' + esc(acc
                            .description) + '</p>' : '',
                        '<div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-muted">',
                        '<span>' + acc.min_persons + '-' + acc.max_persons + ' personen</span>',
                        acc.price_per_night > 0 ? '<span class="font-semibold" style="color:' + typeKleur +
                        '">' + prijs + ' ' + window.__('reserve.per_night') + '</span>' : '',
                        '<span class="inline-flex items-center gap-1.5"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:' +
                        statusKleurWaarde + '"></span>' + statusLabel + '</span>',
                        '</div>',
                        '<div class="mt-auto pt-3 border-t border-border flex justify-end">',
                        (vrij && acc.id) ? '<button type="button" data-id="' + acc.id +
                        '" data-title="' + esc(acc.title) +
                        '" class="reserveer-btn bg-accent hover:bg-accent-hover text-white font-medium px-6 py-2.5 rounded-lg transition text-sm border-0 cursor-pointer">Reserveer Nu</button>' :
                        '<span class="text-sm text-muted italic">Deze accommodatie is momenteel niet beschikbaar voor reservering.</span>',
                        '</div>',
                        '</div>',
                        '</div>',
                    ].join('');

                    detailKaart.classList.remove('hidden');

                    document.getElementById('detail-close').addEventListener('click', function(e) {
                        e.stopPropagation();
                        sluitDetail();
                    });

                    detailKaart.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }

                // --- Fit bounds ---
                var groep = L.featureGroup(
                    items.map(function(a) {
                        return L.marker([a.latitude, a.longitude]);
                    })
                );
                kaart.fitBounds(groep.getBounds().pad(0.1));

                function esc(str) {
                    if (typeof str !== 'string') return str;
                    var d = document.createElement('div');
                    d.appendChild(document.createTextNode(str));
                    return d.innerHTML;
                }
            })();
        });

        // --- Reserveer Modal ---
        function openBoekModal(id, titel) {
            document.getElementById('modal-accommodation-id').value = id;
            document.getElementById('modal-title').textContent = window.__('reserve.modal_title').replace('{name}', titel);
            document.getElementById('booking-modal').classList.remove('hidden');
            document.getElementById('booking-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.getElementById('booking-error').classList.add('hidden');
            document.getElementById('booking-error').textContent = '';

            var vandaag = new Date();
            vandaag.setHours(0, 0, 0, 0);
            var morgen = new Date(vandaag);
            morgen.setDate(morgen.getDate() + 1);
            var overmorgen = new Date(vandaag);
            overmorgen.setDate(overmorgen.getDate() + 2);

            var aankomstInput = document.getElementById('arrival-date');
            var vertrekInput = document.getElementById('departure-date');
            aankomstInput.min = morgen.toISOString().split('T')[0];
            vertrekInput.min = overmorgen.toISOString().split('T')[0];
            if (aankomstInput && !aankomstInput.value) {
                aankomstInput.value = morgen.toISOString().split('T')[0];
            }
            if (vertrekInput && !vertrekInput.value) {
                vertrekInput.value = overmorgen.toISOString().split('T')[0];
            }
        }

        function sluitBoekModal() {
            document.getElementById('booking-modal').classList.add('hidden');
            document.getElementById('booking-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Event delegation for reserveer buttons (created dynamically)
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.reserveer-btn');
            if (btn) {
                openBoekModal(btn.getAttribute('data-id'), btn.getAttribute('data-title'));
            }
        });

        // Close modal on backdrop click
        document.getElementById('booking-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                sluitBoekModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                sluitBoekModal();
            }
        });

        // Postcode search and form submission are handled by address.js (loaded via app.js)

        // Auto-open modal if navigated from home page with accommodation ID
        (function() {
            var params = new URLSearchParams(window.location.search);
            var accId = params.get('accommodatie');
            if (accId && window.ACCOMMODATIONS) {
                var acc = window.ACCOMMODATIONS.find(function(a) {
                    return String(a.id) === accId;
                });
                if (acc) {
                    openBookingModal(acc.id, acc.title);
                }
            }
        })();
    </script>
@endsection
