<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Support\Facades\Config;

class ReservationController extends Controller
{
    public function index()
    {
        $locale = getLocale();
        $accommodations = Accommodation::all();
        $postcodeApiKey = Config::get('services.postcode.api_key');

        return view('reserveren', compact('accommodations', 'postcodeApiKey', 'locale'));
    }
}
