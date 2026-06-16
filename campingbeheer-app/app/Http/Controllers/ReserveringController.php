<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use Illuminate\Support\Facades\Config;

class ReserveringController extends Controller
{
    public function index()
    {
        $taal = $this->getLocale();
        $accommodaties = Accommodatie::all();
        $postcodeApiSleutel = Config::get('services.postcode.api_key');

        return view('reserveren', compact('accommodaties', 'postcodeApiSleutel', 'taal'));
    }

    private function getLocale(): string
    {
        $ondersteund = ['nl', 'en', 'de', 'fy'];
        $taal = request()->cookie('locale', 'nl');
        if (!in_array($taal, $ondersteund, true)) {
            $taal = 'nl';
        }
        return $taal;
    }
}
