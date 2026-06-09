@extends('layouts.admin')

@section('title', 'Accommodatie aanmaken')

@push('styles')
<style>
#map-form {
    border-radius: 0.75rem;
}
.pin-marker {
    width: 36px;
    height: 36px;
    background: #dc2626;
    border: 4px solid #fff;
    border-radius: 50%;
    box-shadow: 0 3px 14px rgba(0,0,0,0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: bold;
    color: #fff;
}
</style>
@endpush

@section('content')
<section class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary" data-i18n="admin.create.title">Nieuwe accommodatie</h1>
        <p class="text-sm text-muted mt-1" data-i18n="admin.create.map_hint">Klik op de kaart om de locatie aan te wijzen.</p>
    </div>

    <form action="{{ route('admin.accommodatie.store') }}" method="POST" class="max-w-3xl">
        @csrf

        <div class="bg-surface border border-border rounded-xl shadow-sm p-6 space-y-5">

            {{-- Title + Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="titel" class="block text-sm font-medium text-primary mb-1">Titel (NL) *</label>
                    <input type="text" name="titel" id="titel" value="{{ old('titel') }}" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('titel') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-primary mb-1">Type (NL) *</label>
                    <select name="type" id="type" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                        <option value="">Kies een type…</option>
                        @foreach (['Blokhut', 'Camping', 'Camperplaats', 'Chalet', 'Safaritent', 'Vakantiehuis', 'Vakantiewoning'] as $opt)
                            <option value="{{ $opt }}" {{ old('type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <input type="hidden" name="titel_en" id="titel_en" value="{{ old('titel_en') }}">
            <input type="hidden" name="titel_de" id="titel_de" value="{{ old('titel_de') }}">
            <input type="hidden" name="titel_fy" id="titel_fy" value="{{ old('titel_fy') }}">

            <input type="hidden" name="type_en" id="type_en" value="{{ old('type_en') }}">
            <input type="hidden" name="type_de" id="type_de" value="{{ old('type_de') }}">
            <input type="hidden" name="type_fy" id="type_fy" value="{{ old('type_fy') }}">

            {{-- Description --}}
            <div>
                <label for="beschrijving" class="block text-sm font-medium text-primary mb-1">Beschrijving (NL)</label>
                <textarea name="beschrijving" id="beschrijving" rows="3"
                    class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">{{ old('beschrijving') }}</textarea>
                @error('beschrijving') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <input type="hidden" name="beschrijving_en" id="beschrijving_en" value="{{ old('beschrijving_en') }}">
            <input type="hidden" name="beschrijving_de" id="beschrijving_de" value="{{ old('beschrijving_de') }}">
            <input type="hidden" name="beschrijving_fy" id="beschrijving_fy" value="{{ old('beschrijving_fy') }}">

            {{-- Persons + Price --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="min_personen" class="block text-sm font-medium text-primary mb-1">Min. personen *</label>
                    <input type="number" name="min_personen" id="min_personen" value="{{ old('min_personen', 1) }}" min="1" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('min_personen') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="max_personen" class="block text-sm font-medium text-primary mb-1">Max. personen *</label>
                    <input type="number" name="max_personen" id="max_personen" value="{{ old('max_personen', 4) }}" min="1" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('max_personen') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="prijs_per_nacht" class="block text-sm font-medium text-primary mb-1">Prijs per nacht (&euro;) *</label>
                    <input type="number" step="0.01" name="prijs_per_nacht" id="prijs_per_nacht" value="{{ old('prijs_per_nacht') }}" min="0" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('prijs_per_nacht') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Image + Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="afbeelding" class="block text-sm font-medium text-primary mb-1">Afbeelding (bestandsnaam)</label>
                    <input type="text" name="afbeelding" id="afbeelding" value="{{ old('afbeelding') }}" placeholder="bv. chalet.jpg"
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('afbeelding') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-primary mb-1">Status *</label>
                    <select name="status" id="status" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                        <option value="beschikbaar" {{ old('status') === 'beschikbaar' ? 'selected' : '' }}>Beschikbaar</option>
                        <option value="niet_beschikbaar" {{ old('status') === 'niet_beschikbaar' ? 'selected' : '' }}>Niet beschikbaar</option>
                    </select>
                    @error('status') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Map --}}
            <div>
                <label class="block text-sm font-medium text-primary mb-1.5">Locatie op kaart</label>
                <div id="map-form" class="w-full" style="height: 380px"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                <p id="coord-display" class="text-xs text-muted mt-1.5">
                    @if (old('latitude') && old('longitude'))
                        {{ old('latitude') }}, {{ old('longitude') }}
                    @else
                        Klik op de kaart om coördinaten in te stellen.
                    @endif
                </p>
                @error('latitude') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

        </div>

        {{-- Submit --}}
        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                class="bg-accent hover:bg-accent-hover text-white font-medium px-6 py-2.5 rounded-lg transition text-sm" data-i18n="admin.create.submit">
                Accommodatie aanmaken
            </button>
            <a href="{{ route('admin.accommodatie.index') }}"
                class="text-muted hover:text-primary font-medium transition text-sm" data-i18n="admin.create.cancel">Annuleren</a>
        </div>
    </form>
</section>
@endsection

@section('scripts')
<script>
async function autoTranslate(sourceFieldId, targets) {
    var el = document.getElementById(sourceFieldId);
    if (!el) return;
    var text = el.value.trim();
    if (!text) return;
    for (var pair of targets) {
        var tgt = document.getElementById(pair.id);
        if (!tgt) continue;
        tgt.value = '...';
        try {
            var res = await fetch('https://libretranslate.com/translate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ q: text, source: 'nl', target: pair.lang })
            });
            var data = await res.json();
            tgt.value = data.translatedText || text;
        } catch(e) {
            tgt.value = text;
        }
    }
}

function setupAutoTranslate(sourceId, targets) {
    var el = document.getElementById(sourceId);
    if (!el) return;
    var timer;
    el.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { autoTranslate(sourceId, targets); }, 600);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    setupAutoTranslate('titel', [
        { id: 'titel_en', lang: 'en' },
        { id: 'titel_de', lang: 'de' },
        { id: 'titel_fy', lang: 'fy' }
    ]);
    setupAutoTranslate('type', [
        { id: 'type_en', lang: 'en' },
        { id: 'type_de', lang: 'de' },
        { id: 'type_fy', lang: 'fy' }
    ]);
    setupAutoTranslate('beschrijving', [
        { id: 'beschrijving_en', lang: 'en' },
        { id: 'beschrijving_de', lang: 'de' },
        { id: 'beschrijving_fy', lang: 'fy' }
    ]);

(function () {
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var coordDisplay = document.getElementById('coord-display');
    var initialLat = latInput.value ? parseFloat(latInput.value) : 53.0968;
    var initialLng = lngInput.value ? parseFloat(lngInput.value) : 5.6878;

    var map = L.map('map-form', {
        center: [initialLat, initialLng],
        zoom: 17,
        maxZoom: 20,
        minZoom: 14,
        zoomControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    var marker = null;
    var areaColors = {
        'Chalets': '#2A6A4E', 'Blokhutten': '#8B4513', 'Safaritenten': '#D97706',
        'Vakantiehuisjes': '#7C3AED', 'Campings': '#2563EB', 'Parkeerplaats': '#6B7280',
    };

    fetch('/data.geojson')
        .then(function (r) { return r.json(); })
        .then(function (geojson) {
            L.geoJSON(geojson, {
                filter: function (f) { return f.geometry.type === 'Polygon'; },
                style: function (f) {
                    var c = areaColors[f.properties.name] || '#647069';
                    return { color: c, weight: 2, opacity: 0.8, fillColor: c, fillOpacity: 0.12 };
                }
            }).addTo(map);
        });

    var bestaande = @json($accommodaties);
    bestaande.forEach(function (acc) {
        if (acc.latitude && acc.longitude) {
            L.circleMarker([acc.latitude, acc.longitude], {
                radius: 5, color: '#647069', fillColor: '#647069',
                fillOpacity: 0.5, weight: 2, opacity: 0.7,
            }).addTo(map)
            .bindTooltip(acc.titel, { direction: 'top', offset: [0, -6] });
        }
    });

    function placeMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: '<div class="pin-marker">+</div>',
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                })
            }).addTo(map);
        }
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        coordDisplay.textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
    }

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });

    if (latInput.value && lngInput.value) {
        placeMarker(parseFloat(latInput.value), parseFloat(lngInput.value));
    }
})();
});
</script>
@endsection
