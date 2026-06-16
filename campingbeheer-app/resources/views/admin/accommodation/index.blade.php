@extends('layouts.admin')

@section('title', 'Accommodatie beheer')

@section('content')
<section class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-primary" data-i18n="admin.index.title">Accommodaties</h1>
        <a href="{{ route('admin.accommodation.create') }}"
            class="bg-accent hover:bg-accent-hover text-white font-medium px-5 py-2 rounded-lg transition text-sm" data-i18n="admin.index.add_new">
            + Nieuwe accommodatie
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-[#2A6A4E]/10 text-[#2A6A4E] text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-surface border border-border rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-secondary text-muted text-left">
                    <th class="px-4 py-3 font-medium" data-i18n="admin.index.titel">Titel</th>
                    <th class="px-4 py-3 font-medium" data-i18n="admin.index.type">Type</th>
                    <th class="px-4 py-3 font-medium" data-i18n="admin.index.price_per_night">Prijs/nacht</th>
                    <th class="px-4 py-3 font-medium" data-i18n="admin.index.persons">Personen</th>
                    <th class="px-4 py-3 font-medium" data-i18n="admin.index.status">Status</th>
                    <th class="px-4 py-3 font-medium" data-i18n="admin.index.location">Locatie</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($accommodaties as $acc)
                    <tr class="hover:bg-secondary/50 transition">
                        <td class="px-4 py-3 font-medium text-primary">{{ $acc->translatedTitle($locale ?? 'nl') }}</td>
                        <td class="px-4 py-3 text-muted capitalize">{{ $acc->translatedType($locale ?? 'nl') }}</td>
                        <td class="px-4 py-3 text-muted">&euro;{{ number_format($acc->price_per_night, 2) }}</td>
                        <td class="px-4 py-3 text-muted">{{ $acc->min_persons }}-{{ $acc->max_persons }}</td>
                        <td class="px-4 py-3">
                            @if ($acc->status === 'available')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#2A6A4E]/10 text-[#2A6A4E]" data-i18n="admin.index.available">Beschikbaar</span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#BD4C4C]/10 text-[#BD4C4C]" data-i18n="admin.index.not_available">Niet beschikbaar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-muted text-xs font-mono">
                            @if ($acc->latitude && $acc->longitude)
                                {{ number_format($acc->latitude, 5) }}, {{ number_format($acc->longitude, 5) }}
                            @else
                                <span class="text-muted/50">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.accommodation.edit', $acc) }}"
                                class="text-accent hover:text-accent-hover font-medium transition text-sm mr-3" data-i18n="admin.index.edit">Bewerken</a>
                            <form action="{{ route('admin.accommodation.destroy', $acc) }}" method="POST" class="inline"
                               onsubmit="return confirm(window.__('admin.index.confirm_delete').replace('{name}', '{{ $acc->title }}'))"> 
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger hover:text-danger-hover font-medium transition text-sm" data-i18n="admin.index.delete">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-muted">
                            <span data-i18n="admin.index.empty">Geen accommodaties gevonden.</span>
                            <a href="{{ route('admin.accommodation.create') }}" class="text-accent hover:underline" data-i18n="admin.index.add_first">Voeg de eerste toe</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
