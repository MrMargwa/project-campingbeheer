<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PlanningBoardController;
use App\Http\Controllers\ReservationController;
use App\Models\Accommodation;
use App\Models\Feature;
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
    $accommodations = Accommodation::with('features')->get();
    $types = Accommodation::select('type', 'type_en', 'type_de', 'type_fy')
        ->distinct('type')
        ->get()
        ->keyBy('type');
    $features = Feature::all();

    return view('home', compact('accommodations', 'types', 'features', 'locale'));
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