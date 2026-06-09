@extends('layouts.admin')

@section('title', 'Accommodatie bewerken')

@push('styles')
<style>
#map-form {
    border-radius: 0.75rem;
}
</style>
@endpush

@section('content')
<section class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary">{{ $accommodatie->titel }} bewerken</h1>
        <p class="text-sm text-muted mt-1">Pas de gegevens aan of klik op de kaart om de locatie te wijzigen.</p>
    </div>

    <form action="{{ route('admin.accommodatie.update', $accommodatie) }}" method="POST" class="max-w-3xl">
        @csrf
        @method('PUT')

        <div class="bg-surface border border-border rounded-xl shadow-sm p-6 space-y-5">

            {{-- Title + Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="titel" class="block text-sm font-medium text-primary mb-1">Titel *</label>
                    <input type="text" name="titel" id="titel" value="{{ old('titel', $accommodatie->titel) }}" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('titel') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-primary mb-1">Type *</label>
                    <select name="type" id="type" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                        <option value="">Kies een type…</option>
                        @foreach (['Blokhut', 'Camping', 'Camperplaats', 'Chalet', 'Safaritent', 'Vakantiehuis', 'Vakantiewoning'] as $opt)
                            <option value="{{ $opt }}" {{ old('type', $accommodatie->type) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="beschrijving" class="block text-sm font-medium text-primary mb-1">Beschrijving</label>
                <textarea name="beschrijving" id="beschrijving" rows="3"
                    class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">{{ old('beschrijving', $accommodatie->beschrijving) }}</textarea>
                @error('beschrijving') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Persons + Price --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="min_personen" class="block text-sm font-medium text-primary mb-1">Min. personen *</label>
                    <input type="number" name="min_personen" id="min_personen" value="{{ old('min_personen', $accommodatie->min_personen) }}" min="1" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('min_personen') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="max_personen" class="block text-sm font-medium text-primary mb-1">Max. personen *</label>
                    <input type="number" name="max_personen" id="max_personen" value="{{ old('max_personen', $accommodatie->max_personen) }}" min="1" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('max_personen') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="prijs_per_nacht" class="block text-sm font-medium text-primary mb-1">Prijs per nacht (&euro;) *</label>
                    <input type="number" step="0.01" name="prijs_per_nacht" id="prijs_per_nacht" value="{{ old('prijs_per_nacht', $accommodatie->prijs_per_nacht) }}" min="0" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('prijs_per_nacht') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Image + Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="afbeelding" class="block text-sm font-medium text-primary mb-1">Afbeelding (bestandsnaam)</label>
                    @if ($accommodatie->afbeelding)
                        <div class="mb-2">
                            <img src="{{ asset('images/' . $accommodatie->afbeelding) }}" alt="" class="h-24 w-auto rounded-lg object-cover border border-border">
                        </div>
                    @endif
                    <input type="text" name="afbeelding" id="afbeelding" value="{{ old('afbeelding', $accommodatie->afbeelding) }}" placeholder="bv. blokhut-1.jpg"
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('afbeelding') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-primary mb-1">Status *</label>
                    <select name="status" id="status" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                        <option value="beschikbaar" {{ old('status', $accommodatie->status) === 'beschikbaar' ? 'selected' : '' }}>Beschikbaar</option>
                        <option value="niet_beschikbaar" {{ old('status', $accommodatie->status) === 'niet_beschikbaar' ? 'selected' : '' }}>Niet beschikbaar</option>
                    </select>
                    @error('status') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Map --}}
            <div>
                <label class="block text-sm font-medium text-primary mb-1.5">Locatie op kaart</label>
                <div id="map-form" class="w-full" style="height: 380px"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $accommodatie->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $accommodatie->longitude) }}">
                <p id="coord-display" class="text-xs text-muted mt-1.5">
                    @if ($accommodatie->latitude && $accommodatie->longitude)
                        {{ $accommodatie->latitude }}, {{ $accommodatie->longitude }}
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
                Wijzigingen opslaan
            </button>
            <a href="{{ route('admin.accommodatie.index') }}"
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

    var bestaande = @json($accommodaties);
    var huidigId = {{ $accommodatie->id }};
    bestaande.forEach(function (acc) {
        if (acc.id === huidigId) return;
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
