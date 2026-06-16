<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Support\Facades\Config;

class ReservationController extends Controller
{
    public function index()
    {
        $locale = $this->getLocale();
        $accommodations = Accommodation::all();
        $postcodeApiKey = Config::get('services.postcode.api_key');

        return view('reserveren', compact('accommodations', 'postcodeApiKey', 'locale'));
    }

    private function getLocale(): string
    {
        $supported = ['nl', 'en', 'de', 'fy'];
        $locale = request()->cookie('locale', 'nl');
        if (!in_array($locale, $supported, true)) {
            $locale = 'nl';
        }
        return $locale;
    }
}
