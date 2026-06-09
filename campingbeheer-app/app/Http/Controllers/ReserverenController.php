<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use Illuminate\Support\Facades\Config;

class ReserverenController extends Controller
{
    public function index()
    {
        $locale = $this->getLocale();
        $accommodaties = Accommodatie::all();
        $postcodeApiKey = Config::get('services.postcode.api_key');

        return view('reserveren', compact('accommodaties', 'postcodeApiKey', 'locale'));
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
