<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(): View
    {
        $accommodations = Accommodation::orderBy('created_at', 'desc')->get();

        return view('admin.accommodation.index', compact('accommodations'));
    }

    public function create(): View
    {
        $accommodations = Accommodation::whereNotNull('latitude')->whereNotNull('longitude')->get();
        return view('admin.accommodation.create', compact('accommodations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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

        Accommodation::create($validated);

        return redirect()->route('admin.accommodation.index')
            ->with('success', 'Accommodation toegevoegd.');
    }

    public function edit(Accommodation $accommodation): View
    {
        $accommodations = Accommodation::whereNotNull('latitude')->whereNotNull('longitude')->get();
        return view('admin.accommodation.edit', compact('accommodation', 'accommodations'));
    }

    public function update(Request $request, Accommodation $accommodation): RedirectResponse
    {
        $validated = $request->validate([
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

        $accommodation->update($validated);

        return redirect()->route('admin.accommodation.index')
            ->with('success', 'Accommodation bijgewerkt.');
    }

    public function destroy(Accommodation $accommodation): RedirectResponse
    {
        $accommodation->delete();

        return redirect()->route('admin.accommodation.index')
            ->with('success', 'Accommodation verwijderd.');
    }
}
