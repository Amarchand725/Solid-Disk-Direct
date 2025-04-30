<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    SettingController,
    ProductController,
    SubscriberController,
    TestimonialController,
    CustomerController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(SettingController::class)->group(function () {
    Route::get('settings', 'getSetting')->name('settings');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('products', 'index')->name('products');
    Route::get('products/{slug}', 'show')->name('products.show');
});
Route::controller(TestimonialController::class)->group(function () {
    Route::get('testimonials', 'index')->name('testimonials');
});

Route::controller(SubscriberController::class)->group(function () {
    Route::post('subscriber/store', 'store');
});

Route::controller(CustomerController::class)->group(function () {
    Route::post('customer/register', 'store');
    Route::post('customer/login', 'login');
});

Route::middleware('auth:customer')->group(function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::post('/customer/logout', 'logout');
        Route::get('/customer/show', 'show');
        Route::post('/customer/update', 'update');
    });
});