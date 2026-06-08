<?php

use App\Http\Controllers\BoekingController;
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

// Reserveren Routes
Route::get('/reserveren', [ReserverenController::class, 'index'])->name('reserveren');
Route::post('/reserveren', [BoekingController::class, 'store'])->name('reserveren.store');

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

Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.accommodatie.')->controller(\App\Http\Controllers\AccommodatieController::class)->group(function () {
        Route::get('/accommodatie', 'index')->name('index');
        Route::get('/accommodatie/aanmaken', 'create')->name('create');
        Route::post('/accommodatie', 'store')->name('store');
        Route::get('/accommodatie/{accommodatie}/bewerken', 'edit')->name('edit');
        Route::put('/accommodatie/{accommodatie}', 'update')->name('update');
        Route::delete('/accommodatie/{accommodatie}', 'destroy')->name('destroy');
    });
});

Route::get('/admin/planbord', function (Request $request) {
	$types = Accommodatie::select('type')->distinct()->pluck('type');

	$selectedType = $request->input('type');
	$weekOffset = (int) $request->input('week', 0);

	$startOfWeek = now()->startOfWeek()->addWeeks($weekOffset);
	$endOfWeek = $startOfWeek->copy()->endOfWeek();

	$weekNumber = $startOfWeek->weekOfYear;
	$year = $startOfWeek->year;

	$days = [];
	for ($i = 0; $i < 7; $i++) {
		$date = $startOfWeek->copy()->addDays($i);
		$days[] = [
			'label' => $date->locale('nl')->isoFormat('dd'),
			'date' => $date->format('Y-m-d'),
			'day' => $date->format('d-m-Y'),
			'isToday' => $date->isToday(),
		];
	}

	$query = Accommodatie::query();
	if ($selectedType) {
		$query->where('type', $selectedType);
	}
	$accommodaties = $query->orderBy('titel')->get();

	$boekingen = \App\Models\Boeking::with('gebruiker')
		->where('aankomst_datum', '<', $endOfWeek->format('Y-m-d'))
		->where('vertrek_datum', '>', $startOfWeek->format('Y-m-d'))
		->whereIn('accommodatie_id', $accommodaties->pluck('id'))
		->get()
		->groupBy('accommodatie_id');

	return view('planbord.index', compact(
		'types', 'selectedType', 'weekOffset', 'weekNumber', 'year',
		'days', 'accommodaties', 'boekingen'
	));
})->middleware(['auth', 'admin'])
	->name('admin.planbord.index');

Route::post('/login', function (Request $request) {
	$request->validate([
		'email' => 'required|email',
		'password' => 'required|string',
	]);

	$credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

	if (Auth::attempt($credentials)) {
		$request->session()->regenerate();
		return redirect()->route('admin.dashboard')->with('success', 'Welkom terug!');
	}

	return back()->withInput($request->only('email'))->with('error', 'E-mailadres of wachtwoord onjuist. Probeer opnieuw.');
});