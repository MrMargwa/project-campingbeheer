<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanningBoardController extends Controller
{
    public function index(Request $request): View
    {
        $types = Accommodation::select('type')->distinct()->pluck('type');

        $selectedType = $request->input('type');
        $weekOffset = (int) $request->input('week', 0);

        $startOfWeek = now()->startOfWeek()->addWeeks($weekOffset);
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $weekNumber = $startOfWeek->weekOfYear;
        $year = $startOfWeek->year;

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $days[] = [
                'label' => $date->locale('nl')->isoFormat('dd'),
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('d-m-Y'),
                'isToday' => $date->isToday(),
            ];
        }

        $query = Accommodation::query();
        if ($selectedType) {
            $query->where('type', $selectedType);
        }
        $accommodations = $query->orderBy('title')->get();

        $bookings = Booking::with('user')
            ->where('arrival_date', '<', $endOfWeek->format('Y-m-d'))
            ->where('departure_date', '>', $startOfWeek->format('Y-m-d'))
            ->whereIn('accommodation_id', $accommodations->pluck('id'))
            ->whereIn('status', ['approved', 'completed'])
            ->get()
            ->groupBy('accommodation_id');

        return view('planbord.index', compact(
            'types', 'selectedType', 'weekOffset', 'weekNumber', 'year',
            'days', 'accommodations', 'bookings'
        ));
    }
}
