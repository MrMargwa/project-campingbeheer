<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use App\Models\Boeking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanbordController extends Controller
{
    public function index(Request $request): View
    {
        $types = Accommodatie::select('type')->distinct()->pluck('type');

        $geselecteerdType = $request->input('type');
        $weekVerschuiving = (int) $request->input('week', 0);

        $beginWeek = now()->startOfWeek()->addWeeks($weekVerschuiving);
        $eindeWeek = $beginWeek->copy()->endOfWeek();

        $weekNummer = $beginWeek->weekOfYear;
        $jaar = $beginWeek->year;

        $dagen = [];
        for ($i = 0; $i < 7; $i++) {
            $datum = $beginWeek->copy()->addDays($i);
            $dagen[] = [
                'label' => $datum->locale('nl')->isoFormat('dd'),
                'date' => $datum->format('Y-m-d'),
                'day' => $datum->format('d-m-Y'),
                'isToday' => $datum->isToday(),
            ];
        }

        $zoekopdracht = Accommodatie::query();
        if ($geselecteerdType) {
            $zoekopdracht->where('type', $geselecteerdType);
        }
        $accommodaties = $zoekopdracht->orderBy('title')->get();

        $boekingen = Boeking::with('user')
            ->where('arrival_date', '<', $eindeWeek->format('Y-m-d'))
            ->where('departure_date', '>', $beginWeek->format('Y-m-d'))
            ->whereIn('accommodation_id', $accommodaties->pluck('id'))
            ->whereIn('status', ['approved', 'completed'])
            ->get()
            ->groupBy('accommodation_id');

        return view('planbord.index', compact(
            'types', 'geselecteerdType', 'weekVerschuiving', 'weekNummer', 'jaar',
            'dagen', 'accommodaties', 'boekingen'
        ));
    }
}
