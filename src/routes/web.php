<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FrontendController;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/
Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/
// Route::get('/', function () {
//     return view('welcome');
// });
// ✅ Halaman publik
    Route::get('/', [FrontendController::class, 'home'])->name('home');
// ✅ Autentikasi
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ✅ UpdateStatus
Route::post('/checkout/status', [CheckoutController::class, 'updateStatus'])->name('checkout.updateStatus');


// ✅ Halaman yang butuh login
Route::middleware(['auth'])->group(function () {
    Route::get('/pesananresep', [FrontendController::class, 'pesananresep']);
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/{pesanan}/token', [CheckoutController::class, 'tokenFromExisting'])
         ->name('checkout.token');
    Route::get('/pengajuan', [FrontendController::class, 'pengajuan'])->name('frontend.pengajuan');
    Route::get('/pesanan', [FrontendController::class, 'pesanan']);
    Route::post('/pesananresep/kirim', [FrontendController::class, 'submitPesananResep'])->name('pesananresep.submit');
    Route::get('/profile', [FrontendController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [FrontendController::class, 'updateprofile'])->name('profile.update');
    Route::get('/pesanan/{pesanan}/invoice', [FrontendController::class, 'downloadInvoice'])
         ->name('pesanan.invoice');
});

