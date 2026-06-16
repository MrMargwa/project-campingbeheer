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
        $gevalideerd = $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'postal_code' => 'required|string|max:10',
            'house_number' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'number_of_guests' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'arrival_date' => 'required|date|after_or_equal:today',
            'departure_date' => 'required|date|after:arrival_date',
        ]);

        $boekData = [
            'accommodation_id' => $gevalideerd['accommodation_id'],
            'name' => $gevalideerd['name'],
            'email' => $gevalideerd['email'],
            'phone' => $gevalideerd['phone'],
            'postal_code' => $gevalideerd['postal_code'],
            'house_number' => $gevalideerd['house_number'],
            'street' => $gevalideerd['street'],
            'city' => $gevalideerd['city'],
            'country' => $gevalideerd['country'],
            'number_of_persons' => $gevalideerd['number_of_guests'],
            'notes' => $gevalideerd['notes'] ?? '',
            'arrival_date' => $gevalideerd['arrival_date'],
            'departure_date' => $gevalideerd['departure_date'],
            'arrival_time' => 'afternoon',
            'departure_time' => 'morning',
        ];

        $accommodatie = Accommodatie::findOrFail($boekData['accommodation_id']);

        $conflictBestaat = Boeking::where('accommodation_id', $boekData['accommodation_id'])
            ->whereIn('status', ['approved', 'pending'])
            ->where('departure_date', '>=', $boekData['arrival_date'])
            ->where('arrival_date', '<=', $boekData['departure_date'])
            ->exists();

        if ($conflictBestaat) {
            return response()->json([
                'errors' => [
                    'period' => [
                        'Deze accommodatie is al bezet in de gekozen periode.',
                    ],
                ],
            ], 422);
        }

        if ($boekData['number_of_persons'] < $accommodatie->min_persons || $boekData['number_of_persons'] > $accommodatie->max_persons) {
            return response()->json([
                'errors' => [
                    'number_of_guests' => [
                        'Het aantal personen moet tussen ' . $accommodatie->min_persons . ' en ' . $accommodatie->max_persons . ' liggen.',
                    ],
                ],
            ], 422);
        }

        $nachten = \Carbon\Carbon::parse($boekData['arrival_date'])->diffInDays(\Carbon\Carbon::parse($boekData['departure_date']));
        $isAdmin = auth()->check() && auth()->user()->role === 'admin';
        $boekData['status'] = $isAdmin ? 'approved' : 'pending';
        $boekData['total_price'] = $accommodatie->price_per_night * $nachten;

        $boeking = Boeking::create($boekData);

        return response()->json([
            'success' => true,
            'message' => $isAdmin
                ? 'Reservering succesvol aangemaakt!'
                : 'Reservering succesvol aangemaakt! Deze moet nog worden goedgekeurd door de beheerder.',
            'booking' => $boeking,
        ]);
    }

    public function approve(Boeking $boeking)
    {
        $boeking->update(['status' => 'approved']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Reservering van ' . $boeking->name . ' is goedgekeurd.');
    }

    public function reject(Request $request, Boeking $boeking)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $boeking->update([
            'status' => 'cancelled',
            'notes' => $request->rejection_reason
                ? trim($boeking->notes . "\nAfgekeurd: " . $request->rejection_reason)
                : $boeking->notes,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Reservering van ' . $boeking->name . ' is afgekeurd.');
    }

    public function searchGuests(Request $request): JsonResponse
    {
        $zoekopdracht = $request->get('q');

        if (strlen($zoekopdracht) < 2) {
            return response()->json([]);
        }

        $gasten = Boeking::select('name', 'email', 'phone', 'postal_code', 'house_number', 'street', 'city', 'country')
            ->where('name', 'like', '%' . $zoekopdracht . '%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                return strtolower($item->email ?: $item->name);
            })
            ->take(10)
            ->values()
            ->map(function ($boeking) {
                return [
                    'name' => $boeking->name,
                    'email' => $boeking->email,
                    'phone' => $boeking->phone,
                    'postal_code' => $boeking->postal_code,
                    'house_number' => $boeking->house_number,
                    'street' => $boeking->street,
                    'city' => $boeking->city,
                    'country' => $boeking->country,
                ];
            });

        return response()->json($gasten);
    }
}
