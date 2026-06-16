<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoekingController;
use App\Http\Controllers\PlanbordController;
use App\Http\Controllers\ReserveringController;
use App\Models\Accommodatie;
use App\Models\Kenmerk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

function getLocale(): string
{
    $ondersteund = ['nl', 'en', 'de', 'fy'];
    $taal = request()->cookie('locale', 'nl');
    if (!in_array($taal, $ondersteund, true)) {
        $taal = 'nl';
    }
    return $taal;
}

// Home Route
Route::get('/', function () {
    $taal = getLocale();
    $accommodaties = Accommodatie::all();
    $types = Accommodatie::select('type', 'type_en', 'type_de', 'type_fy')
        ->distinct('type')
        ->get()
        ->keyBy('type');
    $kenmerken = Kenmerk::all();
    $postcodeApiSleutel = Config::get('services.postcode.api_key');

    return view('home', compact('accommodaties', 'types', 'kenmerken', 'taal', 'postcodeApiSleutel'));
})->name('home');

// Reservation Routes
Route::get('/reserveren', [ReserveringController::class, 'index'])->name('reservation');
Route::post('/reserveren', [BoekingController::class, 'store'])->name('reservation.store');

// Admin Routes
Route::get('/admin', function () {
    return redirect()->route('login');
})->name('admin');

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

Route::get('/admin/planning-board', [PlanbordController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.planning-board.index');

Route::get('/admin/search-guests', [BoekingController::class, 'searchGuests'])
    ->middleware(['auth', 'admin'])
    ->name('admin.search-guests');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.accommodation.')->controller(\App\Http\Controllers\AccommodatieController::class)->group(function () {
        Route::get('/accommodations', 'index')->name('index');
        Route::get('/accommodations/create', 'create')->name('create');
        Route::post('/accommodations', 'store')->name('store');
        Route::get('/accommodations/{accommodation}/edit', 'edit')->name('edit');
        Route::put('/accommodations/{accommodation}', 'update')->name('update');
        Route::delete('/accommodations/{accommodation}', 'destroy')->name('destroy');
    });

    Route::post('/admin/bookings/{boeking}/approve', [\App\Http\Controllers\BoekingController::class, 'approve'])->name('admin.bookings.approve');
    Route::post('/admin/bookings/{boeking}/reject', [\App\Http\Controllers\BoekingController::class, 'reject'])->name('admin.bookings.reject');
});