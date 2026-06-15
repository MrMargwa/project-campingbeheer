<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use App\Models\Boeking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
            'aankomst_datum' => 'required|date|after_or_equal:today',
            'vertrek_datum' => 'required|date|after:aankomst_datum',
        ]);

        $validated['aankomst_tijd'] = 'middag';
        $validated['vertrek_tijd'] = 'ochtend';

        $accommodatie = Accommodatie::findOrFail($validated['accommodatie_id']);

        // Controleer of er al een actieve boeking (goedgekeurd of in afwachting) is die overlapt met gevraagde periode
        $conflictExists = Boeking::where('accommodatie_id', $validated['accommodatie_id'])
            ->whereIn('status', ['goedgekeurd', 'in_afwachting'])
            ->where('vertrek_datum', '>=', $validated['aankomst_datum'])
            ->where('aankomst_datum', '<=', $validated['vertrek_datum'])
            ->exists();

        if ($conflictExists) {
            return response()->json([
                'errors' => [
                    'periode' => [
                        'Deze accommodatie is al bezet in de gekozen periode.',
                    ],
                ],
            ], 422);
        }

        if ($validated['aantal_personen'] < $accommodatie->min_personen || $validated['aantal_personen'] > $accommodatie->max_personen) {
            return response()->json([
                'errors' => [
                    'aantal_personen' => [
                        'Het aantal personen moet tussen ' . $accommodatie->min_personen . ' en ' . $accommodatie->max_personen . ' liggen.',
                    ],
                ],
            ], 422);
        }

        $validated['status'] = 'in_afwachting';
        $validated['totaal_prijs'] = $accommodatie->prijs_per_nacht;

        $boeking = Boeking::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reservering succesvol aangemaakt! Deze moet nog worden goedgekeurd door de beheerder.',
            'boeking' => $boeking,
        ]);
    }

    public function approve(Boeking $boeking)
    {
        // Controleer op overlap met andere actieve boekingen (in afwachting of goedgekeurd)
        $conflict = Boeking::where('accommodatie_id', $boeking->accommodatie_id)
            ->where('status', '!=', 'geannuleerd')
            ->where('id', '!=', $boeking->id)
            ->where('vertrek_datum', '>=', $boeking->aankomst_datum)
            ->where('aankomst_datum', '<=', $boeking->vertrek_datum)
            ->exists();

        if ($conflict) {
            return redirect()->back()->with('error', 'Kan niet goedkeuren: de accommodatie is al bezet in deze periode.');
        }

        $boeking->update(['status' => 'goedgekeurd']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Reservering van ' . $boeking->naam . ' is goedgekeurd.');
    }

    public function reject(Request $request, Boeking $boeking)
    {
        $request->validate([
            'afkeur_reden' => 'nullable|string|max:1000',
        ]);

        $boeking->update([
            'status' => 'geannuleerd',
            'opmerking' => $request->afkeur_reden
                ? trim($boeking->opmerking . "\nAfgekeurd: " . $request->afkeur_reden)
                : $boeking->opmerking,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Reservering van ' . $boeking->naam . ' is afgekeurd.');
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
