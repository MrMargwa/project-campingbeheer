<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $accommodations = Accommodation::all();
        $postcodeApiKey = Config::get('services.postcode.api_key');
        $today = now()->toDateString();

        $bookings = Booking::with('accommodation')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $todayArrivals = Booking::with('accommodation')
            ->where('arrival_date', $today)
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('arrival_time', 'desc')
            ->get();

        $todayDepartures = Booking::with('accommodation')
            ->where('departure_date', $today)
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('departure_time', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'accommodations', 'postcodeApiKey', 'bookings',
            'todayArrivals', 'todayDepartures'
        ));
    }
}
