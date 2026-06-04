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
Route::view('/admin', 'admin/dashboard')->name('admin');

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
		return redirect()->route('admin')->with('success', 'Welkom terug!');
	}

	return back()->withInput($request->only('naam'))->with('error', 'Gebruikersnaam of wachtwoord onjuist. Probeer opnieuw.');
});