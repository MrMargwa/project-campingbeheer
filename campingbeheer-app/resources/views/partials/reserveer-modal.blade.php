@php
    $showSearch = $showSearch ?? false;
@endphp

<div id="reserveer-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-border px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-lg font-semibold text-primary" id="modal-title">Reserveren</h3>
            <button type="button" onclick="closeReserveerModal()"
                class="text-muted hover:text-primary text-2xl leading-none bg-transparent border-0 cursor-pointer">&times;</button>
        </div>
        <form id="reserveer-form" class="p-6 space-y-4">
            <input type="hidden" name="accommodatie_id" id="modal-accommodatie-id">

            @if ($showSearch)
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Aankomstdatum</label>
                    <input type="date" name="aankomst_datum" id="aankomst-datum" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Aankomst</label>
                    <select name="aankomst_tijd"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                        <option value="ochtend">'s Ochtends</option>
                        <option value="middag">'s Middags</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Vertrekdatum</label>
                    <input type="date" name="vertrek_datum" id="vertrek-datum" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Vertrek</label>
                    <select name="vertrek_tijd"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                        <option value="ochtend">'s Ochtends</option>
                        <option value="middag">'s Middags</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary mb-1">Zoek gast op naam</label>
                <input type="text" id="gast-search" autocomplete="off" placeholder="Typ een naam om te zoeken..."
                    class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                <div id="gast-search-results" class="hidden mt-1 rounded-lg border border-border bg-white shadow-lg max-h-48 overflow-y-auto text-sm"></div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Volledige naam</label>
                <input type="text" name="naam" required
                    class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Email</label>
                    <input type="email" name="email" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Telefoonnummer</label>
                    <input type="tel" name="telefoon" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Postcode</label>
                    <input type="text" name="postcode" id="postcode-input" maxlength="7" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none uppercase"
                        placeholder="1234 AB">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Huisnummer</label>
                    <input type="text" name="huisnummer" id="huisnummer-input" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                </div>
                <div class="flex items-end">
                    <button type="button" id="postcode-zoeken" disabled
                        class="w-full bg-secondary text-primary font-medium px-3 py-2 rounded-lg text-sm border border-border cursor-not-allowed opacity-50">
                        Zoeken
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Straatnaam</label>
                <input type="text" name="straat" id="straat-input" required
                    class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Plaatsnaam</label>
                    <input type="text" name="plaats" id="plaats-input" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-1">Land</label>
                    <input type="text" name="land" value="Nederland" required
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Aantal personen</label>
                <input type="number" name="aantal_personen" min="1" max="99" required
                    class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Opmerking</label>
                <textarea name="opmerking" rows="3"
                    class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:ring-2 focus:ring-accent/30 focus:border-accent outline-none resize-none"></textarea>
            </div>

            <div id="reserveer-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2"></div>

            <button type="submit" id="reserveer-submit"
                class="w-full bg-accent hover:bg-accent-hover text-white font-medium py-2.5 rounded-lg transition text-sm border-0 cursor-pointer">
                Reservering Bevestigen
            </button>
        </form>
    </div>
</div>
