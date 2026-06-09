<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use App\Models\Boeking;
use Illuminate\Http\JsonResponse;
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
            'aankomst_datum' => 'nullable|date',
            'vertrek_datum' => 'nullable|date',
            'aankomst_tijd' => 'nullable|in:ochtend,middag',
            'vertrek_tijd' => 'nullable|in:ochtend,middag',
        ]);

        $accommodatie = Accommodatie::findOrFail($validated['accommodatie_id']);

        if (empty($validated['aankomst_datum'])) {
            $validated['aankomst_datum'] = now()->addDay()->toDateString();
        }
        if (empty($validated['vertrek_datum'])) {
            $validated['vertrek_datum'] = now()->addDays(2)->toDateString();
        }

        $validated['status'] = 'in_afwachting';
        $validated['totaal_prijs'] = $accommodatie->prijs_per_nacht;

        $boeking = Boeking::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reservering succesvol aangemaakt!',
            'boeking' => $boeking,
        ]);
    }

    public function searchGasten(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $gasten = Boeking::select('naam', 'email', 'telefoon', 'postcode', 'huisnummer', 'straat', 'plaats', 'land')
            ->where('naam', 'like', '%' . $query . '%')
            ->orderBy('aangemaakt_op', 'desc')
            ->get()
            ->unique(function ($item) {
                return strtolower($item->email ?: $item->naam);
            })
            ->take(10)
            ->values();

        return response()->json($gasten);
    }
}
