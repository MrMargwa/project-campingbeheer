<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;
use Illuminate\Support\Facades\Config;

class ReserverenController extends Controller
{
    public function index()
    {
        $accommodaties = Accommodatie::all();
        $postcodeApiKey = Config::get('services.postcode.api_key');

        return view('reserveren', compact('accommodaties', 'postcodeApiKey'));
    }
}