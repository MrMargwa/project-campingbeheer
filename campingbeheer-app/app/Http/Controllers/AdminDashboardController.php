<?php

namespace App\Http\Controllers;

use App\Models\Boeking;
use App\Models\Accommodatie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $accommodaties = Accommodatie::all();
        $postcodeApiSleutel = Config::get('services.postcode.api_key');
        $vandaag = now()->toDateString();

        $boekingen = Boeking::with('accommodation')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $vandaagAankomsten = Boeking::with('accommodation')
            ->where('arrival_date', $vandaag)
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('arrival_time', 'desc')
            ->get();

        $vandaagVertrekken = Boeking::with('accommodation')
            ->where('departure_date', $vandaag)
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('departure_time', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'accommodaties', 'postcodeApiSleutel', 'boekingen',
            'vandaagAankomsten', 'vandaagVertrekken'
        ));
    }
}
