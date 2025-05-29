<?php

use Illuminate\Support\Facades\{
    Route,
    Artisan
};

use App\Http\Controllers\{
    WebController,
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::controller(WebController::class)->group(function () {
    Route::get('/-sdd-google-shopping.txt', 'SddGoogleShopping')->name('-sdd-google-shopping.txt');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
