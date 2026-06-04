<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;

class ReserverenController extends Controller
{
    public function index()
    {
        $accommodaties = Accommodatie::all();
        $types = Accommodatie::select('type')->distinct()->pluck('type');

        return view('reserveren', compact('accommodaties', 'types'));
    }
}
