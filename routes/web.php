<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\ParcelController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ParcelController as CustomerParcelController;
use App\Http\Controllers\Customer\ReceiverController as CustomerReceiverController;
use App\Http\Controllers\Branch\DashboardController as BranchDashboardController;
use App\Http\Controllers\Branch\ParcelController as BranchParcelController;
use App\Http\Controllers\Rider\DashboardController as RiderDashboardController;
use App\Http\Controllers\Rider\DeliveryController as RiderDeliveryController;
use App\Support\RoleRedirect;
use Illuminate\Support\Facades\Route;

// ── Public routes ──────────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'));
Route::get('/access-denied', fn () => view('errors.access-denied'))->name('access.denied');

// Backward-compat smart redirect — sends any authenticated user to their role dashboard
Route::middleware('auth')->get('/dashboard', function () {
    return redirect(RoleRedirect::path(auth()->user()->role));
})->name('dashboard');

// ── Profile (all authenticated users) ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin routes ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('customers', CustomerController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('riders', RiderController::class);
    Route::resource('parcels', ParcelController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('parcels/{id}/update-status', [ParcelController::class, 'updateStatus'])->name('parcels.updateStatus');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/operations', [ReportsController::class, 'operations'])->name('operations');
});

// ── Branch Manager routes ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:branch_mgr'])->prefix('branch')->name('branch.')->group(function () {
    Route::get('/dashboard', [BranchDashboardController::class, 'index'])->name('dashboard');
    Route::get('/parcels', [BranchParcelController::class, 'index'])->name('parcels.index');
    Route::get('/parcels/{id}', [BranchParcelController::class, 'show'])->name('parcels.show');
    Route::post('/parcels/{id}/update-status', [BranchParcelController::class, 'updateStatus'])->name('parcels.updateStatus');
});

// ── Rider routes ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:rider'])->prefix('rider')->name('rider.')->group(function () {
    Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');
    Route::get('/delivery/{id}/log', [RiderDeliveryController::class, 'logForm'])->name('delivery.log');
    Route::post('/delivery/{id}/log', [RiderDeliveryController::class, 'logAttempt'])->name('delivery.store');
});

// ── Customer routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/parcels', [CustomerParcelController::class, 'index'])->name('parcels.index');
    Route::get('/parcels/create', [CustomerParcelController::class, 'create'])->name('parcels.create');
    Route::post('/parcels', [CustomerParcelController::class, 'store'])->name('parcels.store');
    Route::get('/parcels/{tracking_code}', [CustomerParcelController::class, 'show'])->name('parcels.show');
    Route::post('/track', [CustomerParcelController::class, 'track'])->name('track');
    Route::get('/receivers', [CustomerReceiverController::class, 'index'])->name('receivers.index');
    Route::get('/receivers/create', [CustomerReceiverController::class, 'create'])->name('receivers.create');
    Route::post('/receivers', [CustomerReceiverController::class, 'store'])->name('receivers.store');
});

require __DIR__.'/auth.php';
