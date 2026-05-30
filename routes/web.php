<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\ParcelController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/track', [TrackingController::class, 'index'])->name('track.form');
Route::post('/track', [TrackingController::class, 'track'])->name('track');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('riders', RiderController::class);
    Route::resource('parcels', ParcelController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('parcels/{id}/update-status', [ParcelController::class, 'updateStatus'])->name('parcels.updateStatus');
});

require __DIR__.'/auth.php';
