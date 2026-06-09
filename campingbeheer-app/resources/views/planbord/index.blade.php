@extends('layouts.admin')

@section('title', 'Planbord')

@section('content')
<section class="mx-auto max-w-7xl p-6">
    <div class="mb-6 flex flex-wrap items-center justify-center gap-4">
        <div class="flex items-center gap-2">
            <select id="type-filter" onchange="filterType(this.value)"
                class="rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary">
                <option value="" data-i18n="planbord.all_types">Alle Verblijven</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ $selectedType === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.planbord.index', ['type' => $selectedType, 'week' => $weekOffset - 1]) }}"
                class="rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary transition hover:bg-secondary">
                &larr;
            </a>
            <span class="min-w-[8rem] text-center text-sm font-medium text-primary">
                Week {{ $weekNumber }}, {{ $year }}
            </span>
            <a href="{{ route('admin.planbord.index', ['type' => $selectedType, 'week' => $weekOffset + 1]) }}"
                class="rounded-lg border border-border bg-white px-3 py-2 text-sm text-primary transition hover:bg-secondary">
                &rarr;
            </a>
        </div>

        <a href="{{ route('admin.planbord.index', ['type' => $selectedType, 'week' => 0]) }}"
            class="rounded-lg border border-border bg-white px-4 py-2 text-sm font-medium text-primary transition hover:bg-secondary {{ $weekOffset === 0 ? 'ring-2 ring-accent/50' : '' }}" data-i18n="planbord.this_week">
            Deze week
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-surface shadow-sm">
        <table class="mx-auto w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-secondary/50">
                    <th class="sticky left-0 z-10 min-w-[11rem] bg-secondary/50 px-4 py-3 text-left font-medium text-muted" data-i18n="planbord.accommodation">Accommodatie</th>
                    @foreach($days as $day)
                        <th class="min-w-[6.5rem] px-3 py-3 text-center font-medium {{ $day['isToday'] ? 'text-accent' : 'text-muted' }}">
                            <div class="text-xs uppercase">{{ $day['label'] }}</div>
                            <div class="mt-0.5 text-base font-semibold">{{ \Carbon\Carbon::parse($day['date'])->format('d-m') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($accommodaties as $accommodatie)
                    @php
                        $accommodatieBoekingen = $boekingen->get($accommodatie->id, collect());
                    @endphp
                    <tr class="border-b border-border last:border-b-0 hover:bg-secondary/20">
                        <td class="sticky left-0 z-10 bg-surface px-4 py-3 font-medium text-primary hover:bg-secondary/20">
                            {{ $accommodatie->titel }}
                        </td>
                        @foreach($days as $day)
                            @php
                                $date = $day['date'];

                                $hasCheckin = false;
                                $hasCheckout = false;
                                $isOccupied = false;
                                $guestNames = [];

                                foreach ($accommodatieBoekingen as $boeking) {
                                    $aankomst = $boeking->aankomst_datum;
                                    $vertrek = $boeking->vertrek_datum;

                                    if ($date === $aankomst) {
                                        $hasCheckin = true;
                                        $guestNames[] = 'In: ' . $boeking->gebruiker->naam;
                                    }
                                    if ($date === $vertrek) {
                                        $hasCheckout = true;
                                        $guestNames[] = 'Uit: ' . $boeking->gebruiker->naam;
                                    }
                                    if ($date > $aankomst && $date < $vertrek) {
                                        $isOccupied = true;
                                        $guestNames[] = $boeking->gebruiker->naam;
                                    }
                                }

                                if ($hasCheckin && $hasCheckout) {
                                    $cellClass = 'diagonal-wissel';
                                    $label = '';
                                    $title = implode(' | ', $guestNames);
                                } elseif ($hasCheckin) {
                                    $cellClass = 'diagonal-checkin';
                                    $label = '';
                                    $title = implode(' | ', $guestNames);
                                } elseif ($hasCheckout) {
                                    $cellClass = 'diagonal-checkout';
                                    $label = '';
                                    $title = implode(' | ', $guestNames);
                                } elseif ($isOccupied) {
                                    $cellClass = 'bg-red-300';
                                    $label = '';
                                    $title = implode(' | ', $guestNames);
                                } else {
                                    $cellClass = 'bg-green-200';
                                    $label = '';
                                    $title = 'Beschikbaar';
                                }
                            @endphp
                            <td class="px-3 py-3 text-center align-middle {{ $cellClass }}" title="{{ $title }}" data-i18n-title="planbord.available">{{ $label }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-muted" data-i18n="planbord.empty">Geen accommodaties gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-muted">
        <div class="flex items-center gap-2">
            <span class="inline-block h-4 w-4 rounded bg-green-200"></span>
            <span data-i18n="planbord.legend.available_all_day">Hele dag beschikbaar</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block h-4 w-4 rounded bg-red-300"></span>
            <span data-i18n="planbord.legend.occupied_all_day">Hele dag bezet</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block h-4 w-4 rounded" style="background: linear-gradient(to bottom right, #bbf7d0 50%, #fca5a5 50%);"></span>
            <span data-i18n="planbord.legend.checkin">Check-in (ochtend vrij, middag bezet)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block h-4 w-4 rounded" style="background: linear-gradient(to bottom right, #fca5a5 50%, #bbf7d0 50%);"></span>
            <span data-i18n="planbord.legend.checkout">Check-out (ochtend bezet, middag vrij)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block h-4 w-4 rounded" style="background: linear-gradient(to top left, #fca5a5 50%, transparent 50%), linear-gradient(to top right, #fca5a5 50%, transparent 50%), #bbf7d0;"></span>
            <span data-i18n="planbord.legend.changeover">Wisseldag (uit + in)</span>
        </div>
    </div>
</section>

<style>
    .bg-green-200 { background-color: #bbf7d0; }
    .bg-red-300 { background-color: #fca5a5; }
    .diagonal-checkin {
        background: linear-gradient(to bottom right, #bbf7d0 50%, #fca5a5 50%);
    }
    .diagonal-checkout {
        background: linear-gradient(to bottom right, #fca5a5 50%, #bbf7d0 50%);
    }
    .diagonal-wissel {
        background:
            linear-gradient(to top left, #fca5a5 50%, transparent 50%),
            linear-gradient(to top right, #fca5a5 50%, transparent 50%),
            #bbf7d0;
    }
    td.bg-green-200, td.bg-red-300, td.diagonal-checkin, td.diagonal-checkout, td.diagonal-wissel {
        cursor: default;
    }
</style>

<script>
    function filterType(value) {
        const params = new URLSearchParams(window.location.search);
        params.set('type', value);
        params.set('week', '{{ $weekOffset }}');
        window.location.search = params.toString();
    }
</script>

@endsection