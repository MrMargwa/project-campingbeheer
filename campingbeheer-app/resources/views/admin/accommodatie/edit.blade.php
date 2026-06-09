@extends('layouts.admin')

@section('title', 'Accommodatie bewerken')

@push('styles')
<style>
#map-form {
    border-radius: 0.75rem;
}
#map-form .leaflet-div-icon {
    background: none !important;
    border: none !important;
}
.pin-marker {
    width: 24px;
    height: 24px;
    background: #2A6A4E;
    border: 3px solid #fff;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
</style>
@endpush

@section('content')
<section class="p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary" data-i18n="admin.edit.title">{{ $accommodatie->titel }} bewerken</h1>
        <p class="text-sm text-muted mt-1" data-i18n="admin.edit.map_hint">Pas de gegevens aan of klik op de kaart om de locatie te wijzigen.</p>
    </div>

    <form action="{{ route('admin.accommodatie.update', $accommodatie) }}" method="POST" class="max-w-3xl">
        @csrf
        @method('PUT')

        <div class="bg-surface border border-border rounded-xl shadow-sm p-6 space-y-5">

            {{-- Title + Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="titel" class="block text-sm font-medium text-primary mb-1">Titel (NL) *</label>
                    <input type="text" name="titel" id="titel" value="{{ old('titel', $accommodatie->titel) }}" required
                        class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">
                    @error('titel') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-primary mb-1">Type (NL) *</label>
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

            <input type="hidden" name="titel_en" id="titel_en" value="{{ old('titel_en', $accommodatie->titel_en) }}">
            <input type="hidden" name="titel_de" id="titel_de" value="{{ old('titel_de', $accommodatie->titel_de) }}">
            <input type="hidden" name="titel_fy" id="titel_fy" value="{{ old('titel_fy', $accommodatie->titel_fy) }}">

            <input type="hidden" name="type_en" id="type_en" value="{{ old('type_en', $accommodatie->type_en) }}">
            <input type="hidden" name="type_de" id="type_de" value="{{ old('type_de', $accommodatie->type_de) }}">
            <input type="hidden" name="type_fy" id="type_fy" value="{{ old('type_fy', $accommodatie->type_fy) }}">

            {{-- Description --}}
            <div>
                <label for="beschrijving" class="block text-sm font-medium text-primary mb-1">Beschrijving (NL)</label>
                <textarea name="beschrijving" id="beschrijving" rows="3"
                    class="w-full rounded-lg border border-border bg-primary px-3.5 py-2.5 text-sm text-primary placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent transition">{{ old('beschrijving', $accommodatie->beschrijving) }}</textarea>
                @error('beschrijving') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <input type="hidden" name="beschrijving_en" id="beschrijving_en" value="{{ old('beschrijving_en', $accommodatie->beschrijving_en) }}">
            <input type="hidden" name="beschrijving_de" id="beschrijving_de" value="{{ old('beschrijving_de', $accommodatie->beschrijving_de) }}">
            <input type="hidden" name="beschrijving_fy" id="beschrijving_fy" value="{{ old('beschrijving_fy', $accommodatie->beschrijving_fy) }}">

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
                    <input type="text" name="afbeelding" id="afbeelding" value="{{ old('afbeelding', $accommodatie->afbeelding) }}" placeholder="bv. chalet.jpg"
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
                class="bg-accent hover:bg-accent-hover text-white font-medium px-6 py-2.5 rounded-lg transition text-sm" data-i18n="admin.edit.submit">
                Wijzigingen opslaan
            </button>
            <a href="{{ route('admin.accommodatie.index') }}"
                class="text-muted hover:text-primary font-medium transition text-sm" data-i18n="admin.edit.cancel">Annuleren</a>
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

    function placeMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: '<div class="pin-marker"></div>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
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
