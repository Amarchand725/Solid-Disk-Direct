<?php

use Illuminate\Support\Facades\{
    Route,
    Artisan
};

use App\Http\Controllers\frontend\{
    CartController,
    OrderController,
    UserController,
    WebController,
    SubscribeController,
    CustomerController
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
    Route::get('/', 'index')->name('home');
    Route::get('/single-product/{slug}', 'singleProduct')->name('single-product');
});

Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'cart')->name('cart');
    Route::post('/cart/add', 'addToCart')->name('cart.add');
    Route::post('/cart/quantity', 'updateCartItem')->name('cart.update.quantity');
    Route::delete('/cart/item/{token}', 'removeCartItem')->name('cart.remove.item');
    Route::delete('/cart/clear', 'clearCart')->name('cart.clear');

    Route::get('/cart/checkout', 'checkout')->name('cart.checkout');
});

Route::controller(OrderController::class)->group(function () {
    Route::post('/order/store', 'placeOrder')->name('order.store');
    Route::get('/order/order-success', 'orderSuccess')->name('order.success');
});

Route::controller(CustomerController::class)->group(function () {
    Route::get('/customer/create', 'create')->name('customer.create');
    Route::post('/customer/store', 'store')->name('customer.store');

    Route::get('/customer/login', 'loginForm')->name('customer.login');
    Route::post('/customer/login', 'login')->name('customer.login');
    Route::get('/customer/logout', 'logout')->name('customer.logout');
});

Route::controller(SubscribeController::class)->group(function () {
    Route::post('/subscribe/store', 'store')->name('subscribe.store');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
