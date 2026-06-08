<?php

namespace App\Http\Controllers;

use App\Models\Accommodatie;

class ReserverenController extends Controller
{
    public function index()
    {
        $accommodaties = Accommodatie::all();

        return view('reserveren', compact('accommodaties'));
    }
}