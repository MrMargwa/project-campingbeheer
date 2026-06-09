@extends('layouts.admin')

@section('title', 'Planbord')

@section('content')
<section class="mx-auto max-w-7xl p-4">
    <div class="mb-4 flex flex-wrap items-center justify-center gap-2 text-xs">
        <select id="type-filter" onchange="filterType(this.value)"
            class="rounded-lg border border-border bg-white px-2 py-1.5 text-xs text-primary">
            <option value="">Alle Verblijven</option>
            @foreach($types as $type)
                <option value="{{ $type }}" {{ $selectedType === $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>

        <div class="flex items-center gap-1">
            <a href="{{ route('admin.planbord.index', ['type' => $selectedType, 'week' => $weekOffset - 1]) }}"
                class="rounded-lg border border-border bg-white px-2 py-1.5 text-xs text-primary transition hover:bg-secondary">
                &larr;
            </a>
            <span class="min-w-[6rem] text-center text-xs font-medium text-primary">
                Week {{ $weekNumber }}, {{ $year }}
            </span>
            <a href="{{ route('admin.planbord.index', ['type' => $selectedType, 'week' => $weekOffset + 1]) }}"
                class="rounded-lg border border-border bg-white px-2 py-1.5 text-xs text-primary transition hover:bg-secondary">
                &rarr;
            </a>
        </div>

        <a href="{{ route('admin.planbord.index', ['type' => $selectedType, 'week' => 0]) }}"
            class="rounded-lg border border-border bg-white px-3 py-1.5 text-xs font-medium text-primary transition hover:bg-secondary {{ $weekOffset === 0 ? 'ring-2 ring-accent/50' : '' }}">
            Deze week
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-border bg-surface shadow-sm">
        <table class="mx-auto w-full text-xs">
            <thead>
                <tr class="border-b border-border bg-secondary/50">
                    <th class="sticky left-0 z-10 min-w-[8rem] bg-secondary/50 px-2 py-1.5 text-left font-medium text-muted">Accommodatie</th>
                    @foreach($days as $day)
                        <th class="min-w-[4rem] border-r border-border px-1 py-1.5 text-center font-medium last:border-r-0 {{ $day['isToday'] ? 'text-accent' : 'text-muted' }}">
                            <div class="text-[10px] uppercase leading-tight">{{ $day['label'] }}</div>
                            <div class="mt-0 leading-tight font-semibold">{{ \Carbon\Carbon::parse($day['date'])->format('d-m') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($accommodaties as $accommodatie)
                    @php
                        $accommodatieBoekingen = $boekingen->get($accommodatie->id, collect());
                    @endphp
                    <tr class="border-b border-border last:border-b-0">
                        <td class="sticky left-0 z-10 bg-surface px-2 py-1 font-medium text-primary">
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
                                    $gast = $boeking->gebruiker?->naam ?? 'Onbekend';

                                    if ($date === $aankomst) {
                                        $hasCheckin = true;
                                        $guestNames[] = 'In: ' . $gast;
                                    }
                                    if ($date === $vertrek) {
                                        $hasCheckout = true;
                                        $guestNames[] = 'Uit: ' . $gast;
                                    }
                                    if ($date > $aankomst && $date < $vertrek) {
                                        $isOccupied = true;
                                        $guestNames[] = $gast;
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
                                    $cellClass = 'bg-red-400';
                                    $label = '';
                                    $title = implode(' | ', $guestNames);
                                } else {
                                    $cellClass = 'bg-green-300';
                                    $label = '';
                                    $title = 'Beschikbaar';
                                }
                            @endphp
                            <td class="border-r border-border px-1 py-1 text-center align-middle last:border-r-0 {{ $cellClass }}" title="{{ $title }}">{{ $label }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-2 py-8 text-center text-muted">Geen accommodaties gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-[10px] text-muted">
        <div class="flex items-center gap-1">
            <span class="inline-block h-3 w-3 rounded-sm bg-green-300"></span>
            Beschikbaar
        </div>
        <div class="flex items-center gap-1">
            <span class="inline-block h-3 w-3 rounded-sm bg-red-400"></span>
            Bezet
        </div>
        <div class="flex items-center gap-1">
            <span class="inline-block h-3 w-3 rounded-sm" style="background: linear-gradient(to bottom right, #86efac 50%, #f87171 50%);"></span>
            Check-in
        </div>
        <div class="flex items-center gap-1">
            <span class="inline-block h-3 w-3 rounded-sm" style="background: linear-gradient(to bottom right, #f87171 50%, #86efac 50%);"></span>
            Check-out
        </div>
        <div class="flex items-center gap-1">
            <span class="inline-block h-3 w-3 rounded-sm" style="background: linear-gradient(to top left, #f87171 50%, transparent 50%), linear-gradient(to top right, #f87171 50%, transparent 50%), #86efac;"></span>
            Wisseldag
        </div>
    </div>
</section>

<style>
    .bg-green-200 { background-color: #bbf7d0; }
    .bg-green-300 { background-color: #86efac; }
    .bg-red-300 { background-color: #fca5a5; }
    .bg-red-400 { background-color: #f87171; }
    .diagonal-checkin {
        background: linear-gradient(to bottom right, #86efac 50%, #f87171 50%);
    }
    .diagonal-checkout {
        background: linear-gradient(to bottom right, #f87171 50%, #86efac 50%);
    }
    .diagonal-wissel {
        background:
            linear-gradient(to top left, #f87171 50%, transparent 50%),
            linear-gradient(to top right, #f87171 50%, transparent 50%),
            #86efac;
    }
    td.bg-green-200, td.bg-green-300, td.bg-red-300, td.bg-red-400,
    td.diagonal-checkin, td.diagonal-checkout, td.diagonal-wissel {
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