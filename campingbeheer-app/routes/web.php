<?php

use App\Http\Controllers\ReserverenController;
use Illuminate\Support\Facades\Route;

// Home Route
Route::view('/', 'home')->name('home');

// Reserveren Route
Route::get('/reserveren', [ReserverenController::class, 'index'])->name('reserveren');

// Admin Route
Route::view('/admin', 'admin/dashboard')->name('admin');