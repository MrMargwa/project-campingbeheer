<?php

use Illuminate\Support\Facades\Route;

// Home Route
Route::view('/', 'home')->name('home');

// Reserveren Route
Route::view('/reserveren', 'reserveren')->name('reserveren');

// Admin Route
Route::view('/admin', 'admin')->name('admin');