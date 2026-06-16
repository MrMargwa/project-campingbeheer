<?php

use App\Http\Controllers\BoekingController;
use App\Http\Controllers\ReserverenController;
use App\Models\Accommodatie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

function getLocale(): string
{
    $supported = ['nl', 'en', 'de', 'fy'];
    $locale = request()->cookie('locale', 'nl');
    if (!in_array($locale, $supported, true)) {
        $locale = 'nl';
    }
    return $locale;
}

// Home Route
Route::get('/', function () {
    $locale = getLocale();
    $accommodaties = Accommodatie::all();
    $types = Accommodatie::select('type', 'type_en', 'type_de', 'type_fy')
        ->distinct('type')
        ->get()
        ->keyBy('type');
    $postcodeApiKey = Config::get('services.postcode.api_key');

    return view('home', compact('accommodaties', 'types', 'locale', 'postcodeApiKey'));
})->name('home');

// Reservation Routes
Route::get('/reserveren', [ReservationController::class, 'index'])->name('reservation');
Route::post('/reserveren', [BookingController::class, 'store'])->name('reservation.store');

// Admin Routes
Route::get('/admin', function () {
    return redirect()->route('login');
})->name('admin');

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

Route::get('/admin/planning-board', [PlanningBoardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.planning-board.index');

Route::get('/admin/search-guests', [BookingController::class, 'searchGuests'])
    ->middleware(['auth', 'admin'])
    ->name('admin.search-guests');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.accommodation.')->controller(\App\Http\Controllers\AccommodationController::class)->group(function () {
        Route::get('/accommodations', 'index')->name('index');
        Route::get('/accommodations/create', 'create')->name('create');
        Route::post('/accommodations', 'store')->name('store');
        Route::get('/accommodations/{accommodation}/edit', 'edit')->name('edit');
        Route::put('/accommodations/{accommodation}', 'update')->name('update');
        Route::delete('/accommodations/{accommodation}', 'destroy')->name('destroy');
    });

    Route::post('/admin/bookings/{booking}/approve', [\App\Http\Controllers\BookingController::class, 'approve'])->name('admin.bookings.approve');
    Route::post('/admin/bookings/{booking}/reject', [\App\Http\Controllers\BookingController::class, 'reject'])->name('admin.bookings.reject');
});