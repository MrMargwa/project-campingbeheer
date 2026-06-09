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
        $accommodaties = Accommodatie::orderBy('aangemaakt_op', 'desc')->get();

        return view('admin.accommodatie.index', compact('accommodaties'));
    }

    public function create(): View
    {
        return view('admin.accommodatie.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titel'           => 'required|string|max:255',
            'titel_en'        => 'nullable|string|max:255',
            'titel_de'        => 'nullable|string|max:255',
            'titel_fy'        => 'nullable|string|max:255',
            'type'            => 'required|string|max:255',
            'type_en'         => 'nullable|string|max:255',
            'type_de'         => 'nullable|string|max:255',
            'type_fy'         => 'nullable|string|max:255',
            'beschrijving'    => 'nullable|string',
            'beschrijving_en' => 'nullable|string',
            'beschrijving_de' => 'nullable|string',
            'beschrijving_fy' => 'nullable|string',
            'min_personen'    => 'required|integer|min:1',
            'max_personen'    => 'required|integer|min:1|gte:min_personen',
            'prijs_per_nacht' => 'required|numeric|min:0',
            'afbeelding'      => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'status'          => 'required|in:beschikbaar,niet_beschikbaar',
        ]);

        Accommodatie::create($validated);

        return redirect()->route('admin.accommodatie.index')
            ->with('success', 'Accommodatie toegevoegd.');
    }

    public function edit(Accommodatie $accommodatie): View
    {
        return view('admin.accommodatie.edit', compact('accommodatie'));
    }

    public function update(Request $request, Accommodatie $accommodatie): RedirectResponse
    {
        $validated = $request->validate([
            'titel'           => 'required|string|max:255',
            'titel_en'        => 'nullable|string|max:255',
            'titel_de'        => 'nullable|string|max:255',
            'titel_fy'        => 'nullable|string|max:255',
            'type'            => 'required|string|max:255',
            'type_en'         => 'nullable|string|max:255',
            'type_de'         => 'nullable|string|max:255',
            'type_fy'         => 'nullable|string|max:255',
            'beschrijving'    => 'nullable|string',
            'beschrijving_en' => 'nullable|string',
            'beschrijving_de' => 'nullable|string',
            'beschrijving_fy' => 'nullable|string',
            'min_personen'    => 'required|integer|min:1',
            'max_personen'    => 'required|integer|min:1|gte:min_personen',
            'prijs_per_nacht' => 'required|numeric|min:0',
            'afbeelding'      => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'status'          => 'required|in:beschikbaar,niet_beschikbaar',
        ]);

        $accommodatie->update($validated);

        return redirect()->route('admin.accommodatie.index')
            ->with('success', 'Accommodatie bijgewerkt.');
    }

    public function destroy(Accommodatie $accommodatie): RedirectResponse
    {
        $accommodatie->delete();

        return redirect()->route('admin.accommodatie.index')
            ->with('success', 'Accommodatie verwijderd.');
    }
}
