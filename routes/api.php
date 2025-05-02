<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    BrandController,
    SettingController,
    ProductController,
    SubscriberController,
    TestimonialController,
    CustomerController,
    BannerController,
    BlogController,
    CategoryController,
    SliderController,
    ShippingMethodController,
    QuestionAnswerController,
    PaymentMethodController,
    PrivacyPolicyController,
    ReturnPolicyController,
    TermAndConditionController,
    QuoteRequestController
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
    Route::get('business-info', 'businessInfo')->name('business-info');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('products', 'index')->name('products');
    Route::get('products/featured', 'featured')->name('products.featured');
    Route::get('products/recent-viewed', 'recentViewed')->name('products.recent-viewed');
    Route::get('products/best-selling', 'bestSelling')->name('products.best-selling');
    Route::get('products/top-rated', 'topRated')->name('products.top-rated');
    Route::get('products/show/{slug}', 'show')->name('products.show');
    Route::get('products/search', 'search')->name('products.search');
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
Route::controller(BrandController::class)->group(function () {
    Route::get('brands', 'index');
    Route::get('brands/show/{slug}', 'show');
    Route::get('brands/featured', 'featured');
});
Route::controller(BannerController::class)->group(function () {
    Route::get('banners', 'index');
});
Route::controller(CategoryController::class)->group(function () {
    Route::get('categories', 'index');
    Route::get('categories/show/{slug}', 'show');
    Route::get('categories/featured', 'featured');
});
Route::controller(SliderController::class)->group(function () {
    Route::get('sliders', 'index');
});
Route::controller(BlogController::class)->group(function () {
    Route::get('blogs', 'index');
    Route::get('blogs/show/{slug}', 'show');
});
Route::controller(ShippingMethodController::class)->group(function () {
    Route::get('shipping_methods', 'index');
});
Route::controller(QuestionAnswerController::class)->group(function () {
    Route::get('question_answers', 'index');
});
Route::controller(PaymentMethodController::class)->group(function () {
    Route::get('payment_methods', 'index');
});
Route::controller(PrivacyPolicyController::class)->group(function () {
    Route::get('privacy_policies', 'index');
});
Route::controller(ReturnPolicyController::class)->group(function () {
    Route::get('return_policies', 'index');
});
Route::controller(TermAndConditionController::class)->group(function () {
    Route::get('term_and_conditions', 'index');
});
Route::controller(QuoteRequestController::class)->group(function () {
    Route::post('quote_requests/store', 'store');
});

Route::middleware('auth:customer')->group(function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::post('/customer/logout', 'logout');
        Route::get('/customer/show', 'show');
        Route::post('/customer/update', 'update');
    });
});