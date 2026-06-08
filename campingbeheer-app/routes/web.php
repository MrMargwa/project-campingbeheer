<?php

use App\Http\Controllers\ReserverenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Home Route
Route::view('/', 'home')->name('home');

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

Route::view('/admin/planbord', 'planbord.index')
	->middleware(['auth', 'admin'])
	->name('admin.planbord.index');

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