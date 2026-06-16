@extends('layouts.admin')

@section('title', 'Accommodatie aanmaken')

@push('styles')
<style>
#map-form {
    border-radius: 0.75rem;
}
.new-pin {
    background: #dc2626 !important;
    border: 4px solid #fff !important;
    border-radius: 50% !important;
    box-shadow: 0 3px 14px rgba(0,0,0,0.45) !important;
    font-size: 22px;
    font-weight: bold;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
</style>
@endpush

@section('content')
<section class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary">Nieuwe accommodatie</h1>
        <p class="text-sm text-muted mt-1">Klik op de kaart om de locatie aan te wijzen.</p>
    </div>

    <form action="{{ route('admin.accommodation.store') }}" method="POST" class="max-w-3xl">
        @csrf

        <div class="bg-surface border border-border rounded-xl shadow-sm p-6 space-y-5">

            {{-- Title + Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-primary mb-1">Titel *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-primary mb-1">Type *</label>
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

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-primary mb-1">Beschrijving</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Persons + Price --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="min_persons" class="block text-sm font-medium text-primary mb-1">Min. personen *</label>
                    <input type="number" name="min_persons" id="min_persons" value="{{ old('min_persons', 1) }}" min="1" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('min_persons') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="max_persons" class="block text-sm font-medium text-primary mb-1">Max. personen *</label>
                    <input type="number" name="max_persons" id="max_persons" value="{{ old('max_persons', 4) }}" min="1" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('max_persons') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="price_per_night" class="block text-sm font-medium text-primary mb-1">Prijs per nacht (&euro;) *</label>
                    <input type="number" step="0.01" name="price_per_night" id="price_per_night" value="{{ old('price_per_night') }}" min="0" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('price_per_night') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Image + Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="image" class="block text-sm font-medium text-primary mb-1">Afbeelding (bestandsnaam)</label>
                    <input type="text" name="image" id="image" value="{{ old('image') }}" placeholder="bv. blokhut-1.jpg"
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('image') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-primary mb-1">Status *</label>
                    <select name="status" id="status" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                        <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Beschikbaar</option>
                        <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>Niet beschikbaar</option>
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
                class="bg-accent hover:bg-accent-hover text-white font-medium px-6 py-2.5 rounded-lg transition text-sm">
                Accommodatie aanmaken
            </button>
            <a href="{{ route('admin.accommodation.index') }}"
                class="text-muted hover:text-primary font-medium transition text-sm">Annuleren</a>
        </div>
    </form>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    var existing = @json($accommodations);
    existing.forEach(function (acc) {
        if (acc.latitude && acc.longitude) {
            L.circleMarker([acc.latitude, acc.longitude], {
                radius: 5, color: '#647069', fillColor: '#647069',
                fillOpacity: 0.5, weight: 2, opacity: 0.7,
            }).addTo(map)
            .bindTooltip(acc.title, { direction: 'top', offset: [0, -6] });
        }
    });

    function placeMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.circleMarker([lat, lng], {
                radius: 18,
                color: '#fff',
                weight: 4,
                fillColor: '#dc2626',
                fillOpacity: 1,
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
