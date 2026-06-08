<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use App\Models\Boeking;
use Illuminate\Http\Request;

class BoekingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'accommodatie_id' => 'required|exists:accommodaties,id',
            'naam' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefoon' => 'required|string|max:20',
            'postcode' => 'required|string|max:10',
            'huisnummer' => 'required|string|max:10',
            'straat' => 'required|string|max:255',
            'plaats' => 'required|string|max:255',
            'land' => 'required|string|max:255',
            'aantal_personen' => 'required|integer|min:1',
            'opmerking' => 'nullable|string|max:1000',
        ]);

        $accommodatie = Accommodatie::findOrFail($validated['accommodatie_id']);

        $validated['status'] = 'in_afwachting';
        $validated['totaal_prijs'] = $accommodatie->prijs_per_nacht;
        $validated['aankomst_datum'] = now()->addDay()->toDateString();
        $validated['vertrek_datum'] = now()->addDays(2)->toDateString();

        $boeking = Boeking::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reservering succesvol aangemaakt!',
            'boeking' => $boeking,
        ]);
    }
}
