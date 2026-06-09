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
                    <th class="sticky left-0 z-10 min-w-[7rem] bg-secondary/50 px-2 py-1.5 text-left font-medium text-muted">Accommodatie</th>
                    @foreach($days as $day)
                        @php
                            $isWeekend = in_array($day['label'], ['za', 'zo']);
                        @endphp
                        <th class="min-w-[2.5rem] border-r border-border px-1 py-1 text-center font-medium last:border-r-0 {{ $day['isToday'] ? 'text-accent' : 'text-muted' }} {{ $isWeekend ? 'bg-secondary/80' : '' }}">
                            <div class="text-[9px] uppercase leading-tight">{{ $day['label'] }}</div>
                            <div class="mt-0 text-[11px] leading-tight font-bold">{{ \Carbon\Carbon::parse($day['date'])->format('d-m') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($accommodaties as $accommodatie)
                    @php
                        $accommodatieBoekingen = $boekingen->get($accommodatie->id, collect());
                    @endphp
                    <tr class="border-b border-border last:border-b-0 hover:bg-black/[0.03]">
                        <td class="sticky left-0 z-10 bg-surface px-2 py-1 font-medium text-primary">
                            {{ $accommodatie->titel }}
                        </td>
                        @foreach($days as $day)
                        @php
                                $date = $day['date'];
                                $isWeekend = in_array($day['label'], ['za', 'zo']);

                                $hasCheckin = false;
                                $hasCheckout = false;
                                $isOccupied = false;
                                $cellBookings = [];

                                foreach ($accommodatieBoekingen as $boeking) {
                                    $aankomst = $boeking->aankomst_datum;
                                    $vertrek = $boeking->vertrek_datum;
                                    $gast = $boeking->gebruiker?->naam ?? $boeking->naam ?? 'Onbekend';

                                    if ($date === $aankomst) {
                                        $hasCheckin = true;
                                    }
                                    if ($date === $vertrek) {
                                        $hasCheckout = true;
                                    }
                                    if ($date > $aankomst && $date < $vertrek) {
                                        $isOccupied = true;
                                    }

                                    if ($date >= $aankomst && $date <= $vertrek) {
                                        $cellBookings[] = [
                                            'naam' => $gast,
                                            'aankomst' => \Carbon\Carbon::parse($boeking->aankomst_datum)->format('d-m-Y'),
                                            'vertrek' => \Carbon\Carbon::parse($boeking->vertrek_datum)->format('d-m-Y'),
                                            'personen' => $boeking->aantal_personen,
                                            'opmerking' => $boeking->opmerking,
                                        ];
                                    }
                                }

                                if ($hasCheckin && $hasCheckout) {
                                    $cellClass = 'diagonal-wissel';
                                    $label = '';
                                } elseif ($hasCheckin) {
                                    $cellClass = 'diagonal-checkin';
                                    $label = '';
                                } elseif ($hasCheckout) {
                                    $cellClass = 'diagonal-checkout';
                                    $label = '';
                                } elseif ($isOccupied) {
                                    $cellClass = 'bg-red-400';
                                    $label = '';
                                } else {
                                    $cellClass = 'bg-green-300';
                                    $label = '';
                                }
                            @endphp
                            <td class="planbord-cell border-r border-border px-0.5 py-0.5 text-center align-middle last:border-r-0 {{ $cellClass }} {{ count($cellBookings) > 0 ? 'cursor-pointer' : '' }}"
                                @if(count($cellBookings) > 0) data-tooltip="{{ json_encode([
                                    'verblijf' => $accommodatie->titel,
                                    'boekingen' => $cellBookings,
                                ]) }}" @endif>
                                <span>{{ $label }}</span>
                            </td>
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
            <span class="inline-block h-3 w-3 rounded-sm" style="background: linear-gradient(to top right, #f87171 50%, #86efac 50%);"></span>
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
        background: linear-gradient(to top right, #f87171 50%, #86efac 50%);
    }
    .diagonal-wissel {
        background:
            linear-gradient(to top left, #f87171 50%, transparent 50%),
            linear-gradient(to top right, #f87171 50%, transparent 50%),
            #86efac;
    }
    tbody tr:hover td.sticky {
        background-color: #f0f2f1;
    }
</style>

<script>
    function filterType(value) {
        const params = new URLSearchParams(window.location.search);
        params.set('type', value);
        params.set('week', '{{ $weekOffset }}');
        window.location.search = params.toString();
    }

    document.addEventListener('DOMContentLoaded', function () {
        let tooltipEl = null;

        document.querySelectorAll('.planbord-cell[data-tooltip]').forEach(function (cell) {
            cell.addEventListener('mouseenter', function (e) {
                var data = JSON.parse(this.getAttribute('data-tooltip'));
                if (!data) return;

                tooltipEl = document.createElement('div');
                tooltipEl.className = 'fixed z-[9999] min-w-[220px] whitespace-nowrap rounded-lg bg-gray-800 px-2.5 py-2 text-left text-xs leading-relaxed text-gray-100 shadow-lg pointer-events-none';

                var html = '';
                for (var i = 0; i < data.boekingen.length; i++) {
                    var b = data.boekingen[i];
                    if (i > 0) {
                        html += '<div class="mt-1.5 border-t border-gray-600 pt-1.5"></div>';
                    }
                    html += '<div><span class="font-semibold text-gray-400">Naam:</span> ' + escHtml(b.naam) + '</div>';
                    html += '<div><span class="font-semibold text-gray-400">Verblijf:</span> ' + escHtml(data.verblijf) + '</div>';
                    html += '<div><span class="font-semibold text-gray-400">Aankomst:</span> ' + escHtml(b.aankomst) + '</div>';
                    html += '<div><span class="font-semibold text-gray-400">Vertrek:</span> ' + escHtml(b.vertrek) + '</div>';
                    html += '<div><span class="font-semibold text-gray-400">Personen:</span> ' + escHtml(b.personen) + '</div>';
                    if (b.opmerking) {
                        html += '<div><span class="font-semibold text-gray-400">Opmerking:</span> ' + escHtml(b.opmerking) + '</div>';
                    }
                }
                html += '<div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>';

                tooltipEl.innerHTML = html;
                document.body.appendChild(tooltipEl);

                var rect = this.getBoundingClientRect();
                var top = rect.top - tooltipEl.offsetHeight - 6;
                var left = rect.left + rect.width / 2 - tooltipEl.offsetWidth / 2;

                if (top < 4) {
                    top = rect.bottom + 6;
                    tooltipEl.querySelector('div:last-child').className = 'absolute top-0 left-1/2 -translate-x-1/2 border-4 border-transparent border-b-gray-800 -translate-y-full';
                }

                if (left < 4) left = 4;
                if (left + tooltipEl.offsetWidth > window.innerWidth - 4) {
                    left = window.innerWidth - tooltipEl.offsetWidth - 4;
                }

                tooltipEl.style.top = top + 'px';
                tooltipEl.style.left = left + 'px';
            });

            cell.addEventListener('mouseleave', function () {
                if (tooltipEl) {
                    tooltipEl.remove();
                    tooltipEl = null;
                }
            });
        });

        function escHtml(str) {
            if (str == null) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }
    });
</script>
@endsection
