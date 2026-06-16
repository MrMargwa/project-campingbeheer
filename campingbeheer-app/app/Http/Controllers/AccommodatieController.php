<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodatieController extends Controller
{
    public function index(): View
    {
        $accommodaties = Accommodatie::orderBy('created_at', 'desc')->get();

        return view('admin.accommodation.index', compact('accommodaties'));
    }

    public function create(): View
    {
        $accommodaties = Accommodatie::whereNotNull('latitude')->whereNotNull('longitude')->get();
        return view('admin.accommodation.create', compact('accommodaties'));
    }

    public function store(Request $request): RedirectResponse
    {
        $gevalideerd = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|string|max:255',
            'description'    => 'nullable|string',
            'min_persons'    => 'required|integer|min:1',
            'max_persons'    => 'required|integer|min:1|gte:min_persons',
            'price_per_night' => 'required|numeric|min:0',
            'image'      => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'status'          => 'required|in:available,unavailable',
        ]);

        Accommodatie::create($gevalideerd);

        return redirect()->route('admin.accommodation.index')
            ->with('success', 'Accommodation toegevoegd.');
    }

    public function edit(Accommodatie $accommodatie): View
    {
        $accommodaties = Accommodatie::whereNotNull('latitude')->whereNotNull('longitude')->get();
        return view('admin.accommodation.edit', compact('accommodatie', 'accommodaties'));
    }

    public function update(Request $request, Accommodatie $accommodatie): RedirectResponse
    {
        $gevalideerd = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|string|max:255',
            'description'    => 'nullable|string',
            'min_persons'    => 'required|integer|min:1',
            'max_persons'    => 'required|integer|min:1|gte:min_persons',
            'price_per_night' => 'required|numeric|min:0',
            'image'      => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'status'          => 'required|in:available,unavailable',
        ]);

        $accommodatie->update($gevalideerd);

        return redirect()->route('admin.accommodation.index')
            ->with('success', 'Accommodation bijgewerkt.');
    }

    public function destroy(Accommodatie $accommodatie): RedirectResponse
    {
        $accommodatie->delete();

        return redirect()->route('admin.accommodation.index')
            ->with('success', 'Accommodation verwijderd.');
    }
}
