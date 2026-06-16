<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        $bookingData = [
            'accommodation_id' => $validated['accommodation_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'postal_code' => $validated['postal_code'],
            'house_number' => $validated['house_number'],
            'street' => $validated['street'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'number_of_persons' => $validated['number_of_guests'],
            'notes' => $validated['notes'] ?? '',
            'arrival_date' => $validated['arrival_date'],
            'departure_date' => $validated['departure_date'],
            'arrival_time' => 'afternoon',
            'departure_time' => 'morning',
        ];

        $accommodation = Accommodation::findOrFail($bookingData['accommodation_id']);

        $conflictExists = Booking::where('accommodation_id', $bookingData['accommodation_id'])
            ->whereIn('status', ['approved', 'pending'])
            ->where('departure_date', '>=', $bookingData['arrival_date'])
            ->where('arrival_date', '<=', $bookingData['departure_date'])
            ->exists();

        if ($conflictExists) {
            return response()->json([
                'errors' => [
                    'period' => [
                        'Deze accommodatie is al bezet in de gekozen periode.',
                    ],
                ],
            ], 422);
        }

        if ($bookingData['number_of_persons'] < $accommodation->min_persons || $bookingData['number_of_persons'] > $accommodation->max_persons) {
            return response()->json([
                'errors' => [
                    'number_of_guests' => [
                        'Het aantal personen moet tussen ' . $accommodation->min_persons . ' en ' . $accommodation->max_persons . ' liggen.',
                    ],
                ],
            ], 422);
        }

        $nights = \Carbon\Carbon::parse($bookingData['arrival_date'])->diffInDays(\Carbon\Carbon::parse($bookingData['departure_date']));
        $bookingData['status'] = 'pending';
        $bookingData['total_price'] = $accommodation->price_per_night * $nights;

        $booking = Booking::create($bookingData);

        return response()->json([
            'success' => true,
            'message' => 'Reservering succesvol aangemaakt! Deze moet nog worden goedgekeurd door de beheerder.',
            'booking' => $booking,
        ]);
    }

    public function approve(Booking $booking)
    {
        $booking->update(['status' => 'approved']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Reservering van ' . $booking->name . ' is goedgekeurd.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'notes' => $request->rejection_reason
                ? trim($booking->notes . "\nAfgekeurd: " . $request->rejection_reason)
                : $booking->notes,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Reservering van ' . $booking->name . ' is afgekeurd.');
    }

    public function searchGuests(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $guests = Booking::select('name', 'email', 'phone', 'postal_code', 'house_number', 'street', 'city', 'country')
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                return strtolower($item->email ?: $item->name);
            })
            ->take(10)
            ->values()
            ->map(function ($booking) {
                return [
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'phone' => $booking->phone,
                    'postal_code' => $booking->postal_code,
                    'house_number' => $booking->house_number,
                    'street' => $booking->street,
                    'city' => $booking->city,
                    'country' => $booking->country,
                ];
            });

        return response()->json($guests);
    }
}
