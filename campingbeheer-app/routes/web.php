<?php

use App\Http\Controllers\ReserverenController;
use App\Models\Accommodatie;
use App\Models\Kenmerk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Home Route
Route::get('/', function () {
    $accommodaties = Accommodatie::with('kenmerken')->get();
    $types = Accommodatie::select('type')->distinct()->pluck('type');
    $kenmerken = Kenmerk::all();

    return view('home', compact('accommodaties', 'types', 'kenmerken'));
})->name('home');

// Reserveren Route
Route::get('/reserveren', [ReserverenController::class, 'index'])->name('reserveren');

// Admin Route
Route::get('/admin', function () {
	return redirect()->route('login');
})->name('admin');

Route::view('/admin/dashboard', 'admin/dashboard')
	->middleware(['auth', 'admin'])
	->name('admin.dashboard');

Route::get('/login', function () {
	return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
	$request->validate([
		'naam' => 'required|string',
		'password' => 'required|string',
	]);

	$credentials = ['naam' => $request->input('naam'), 'password' => $request->input('password')];

	if (Auth::attempt($credentials)) {
		$request->session()->regenerate();
		return redirect()->route('admin.dashboard')->with('success', 'Welkom terug!');
	}

	return back()->withInput($request->only('naam'))->with('error', 'Gebruikersnaam of wachtwoord onjuist. Probeer opnieuw.');
});