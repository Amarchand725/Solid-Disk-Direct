<?php

use Illuminate\Support\Facades\{
    Route,
    Artisan
};
use App\Http\Controllers\Admin\{
    AdminController,
    AttributeController,
    BannerController,
    BlogController,
    BrandController,
    CategoryController,
    ColorController,
    ContactMessageController,
    CouponController,
    CurrencyController,
    CustomerController,
    FlashDealController,
    MenuController,
    MenuFieldController,
    PaymentMethodController,
    PaymentModeController,
    PaymentTypeController,
    PermissionController,
    PolicyController,
    ProductConditionController,
    ProductController,
    QuestionAnswerController,
    QuoteRequestController,
    RecentViewProductController,
    RoleController,
    SettingController,
    ShippingMethodController,
    SliderController,
    SubscriberController,
    TagController,
    TaxTypeController,
    TestimonialController,
    UnitController,
    WishlistController,
    UserController
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

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::get('/cache', function() {
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    $cache = 'Route cache cleared <br /> View cache cleared <br /> Cache cleared <br /> Config cleared <br /> Config cache cleared';
    return $cache;
});

Route::controller(AdminController::class)->group(function () {
    Route::get('admin/login', 'loginForm')->name('admin.login');
    Route::post('admin/login', 'login')->name('admin.login');
    Route::get('/get-states', 'getStates')->name('get-states');
    Route::get('/get-cities', 'getCities')->name('get-cities');
    //API DOCS
    Route::get('/api_docs', 'apiDocs')->name('api_docs.index');
});

Route::middleware('auth')->group(function () {
Route::controller(AdminController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/logout', 'logOut')->name('user.logout');
    });

    //custom routes
    Route::controller(BrandController::class)->group(function () {
        Route::get('brands/trashed', 'trashed')->name('brands.trashed');
        Route::get('brands/restore/{id}', 'restore')->name('brands.restore');
        Route::get('brands/import/create', 'importCreate')->name('brands.import.create');
        Route::post('brands/import/store', 'importStore')->name('brands.import.store');
        Route::post('brands/force-delete/{id}', 'forceDelete')->name('brands.force-delete');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories/trashed', 'trashed')->name('categories.trashed');
        Route::get('categories/restore/{id}', 'restore')->name('categories.restore');
        Route::get('categories/import/create', 'importCreate')->name('categories.import.create');
        Route::post('categories/import/store', 'importStore')->name('categories.import.store');
        Route::get('categories/sub-categories', 'subCategories')->name('categories.sub-categories');
    });
    
    Route::controller(BannerController::class)->group(function () {
        Route::get('banners/trashed', 'trashed')->name('banners.trashed');
        Route::get('banners/restore/{id}', 'restore')->name('banners.restore');
    });
    
    Route::controller(CurrencyController::class)->group(function () {
        Route::get('currencies/trashed', 'trashed')->name('currencies.trashed');
        Route::get('currencies/restore/{id}', 'restore')->name('currencies.restore');
    });
    
    Route::controller(ShippingMethodController::class)->group(function () {
        Route::get('shipping_methods/trashed', 'trashed')->name('shipping_methods.trashed');
        Route::get('shipping_methods/restore/{id}', 'restore')->name('shipping_methods.restore');
        Route::get('shipping_methods/import/create', 'importCreate')->name('shipping_methods.import.create');
        Route::post('shipping_methods/import/store', 'importStore')->name('shipping_methods.import.store');
    });
    
    Route::controller(AttributeController::class)->group(function () {
        Route::get('attributes/trashed', 'trashed')->name('attributes.trashed');
        Route::get('attributes/restore/{id}', 'restore')->name('attributes.restore');
        Route::get('attributes/import/create', 'importCreate')->name('attributes.import.create');
        Route::post('attributes/import/store', 'importStore')->name('attributes.import.store');
    });
    Route::controller(ColorController::class)->group(function () {
        Route::get('colors/trashed', 'trashed')->name('colors.trashed');
        Route::get('colors/restore/{id}', 'restore')->name('colors.restore');
        Route::get('colors/import/create', 'importCreate')->name('colors.import.create');
        Route::post('colors/import/store', 'importStore')->name('colors.import.store');
        Route::delete('colors/forceDelete/{id}', 'forceDelete')->name('colors.forceDelete');
    });
    Route::controller(QuestionAnswerController::class)->group(function () {
        Route::get('question_answers/trashed', 'trashed')->name('question_answers.trashed');
        Route::get('question_answers/restore/{id}', 'restore')->name('question_answers.restore');
        Route::get('question_answers/import/create', 'importCreate')->name('question_answers.import.create');
        Route::post('question_answers/import/store', 'importStore')->name('question_answers.import.store');
    });
    Route::controller(CouponController::class)->group(function () {
        Route::get('coupons/trashed', 'trashed')->name('coupons.trashed');
        Route::get('coupons/restore/{id}', 'restore')->name('coupons.restore');
    });
    Route::controller(FlashDealController::class)->group(function () {
        Route::get('flash_deals/trashed', 'trashed')->name('flash_deals.trashed');
        Route::get('flash_deals/restore/{id}', 'restore')->name('flash_deals.restore');
    });
    Route::controller(TagController::class)->group(function () {
        Route::get('tags/trashed', 'trashed')->name('tags.trashed');
        Route::get('tags/restore/{id}', 'restore')->name('tags.restore');
        Route::get('tags/import/create', 'importCreate')->name('tags.import.create');
        Route::post('tags/import/store', 'importStore')->name('tags.import.store');
        Route::get('tags/search', 'search')->name('tags.search');
    });
    Route::controller(PaymentMethodController::class)->group(function () {
        Route::get('payment_methods/trashed', 'trashed')->name('payment_methods.trashed');
        Route::get('payment_methods/restore/{id}', 'restore')->name('payment_methods.restore');
    });
    Route::controller(ProductController::class)->group(function () {
        Route::get('products/trashed', 'trashed')->name('products.trashed');
        Route::get('products/restore/{id}', 'restore')->name('products.restore');
        Route::get('products/import/create', 'importCreate')->name('products.import.create');
        Route::post('products/import/store', 'importStore')->name('products.import.store');
        Route::delete('products/remove/image/{id}', 'removeImage')->name('products.remove.image');
        Route::delete('products/forceDelete/{id}', 'forceDelete')->name('products.forceDelete');
    });
    Route::controller(UnitController::class)->group(function () {
        Route::get('units/trashed', 'trashed')->name('units.trashed');
        Route::get('units/restore/{id}', 'restore')->name('units.restore');
    });
    Route::controller(TaxTypeController::class)->group(function () {
        Route::get('tax_types/trashed', 'trashed')->name('tax_types.trashed');
        Route::get('tax_types/restore/{id}', 'restore')->name('tax_types.restore');
    });
    Route::controller(PaymentModeController::class)->group(function () {
        Route::get('payment_modes/trashed', 'trashed')->name('payment_modes.trashed');
        Route::get('payment_modes/restore/{id}', 'restore')->name('payment_modes.restore');
    });
    Route::controller(PaymentTypeController::class)->group(function () {
        Route::get('payment_types/trashed', 'trashed')->name('payment_types.trashed');
        Route::get('payment_types/restore/{id}', 'restore')->name('payment_types.restore');
    });
    Route::controller(ProductConditionController::class)->group(function () {
        Route::get('product_conditions/trashed', 'trashed')->name('product_conditions.trashed');
        Route::get('product_conditions/restore/{id}', 'restore')->name('product_conditions.restore');
    });
    Route::controller(SubscriberController::class)->group(function () {
        Route::get('subscribers/trashed', 'trashed')->name('subscribers.trashed');
        Route::get('subscribers/restore/{id}', 'restore')->name('subscribers.restore');
        Route::delete('subscribers/forceDelete/{id}', 'forceDelete')->name('subscribers.forceDelete');
    });
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customers/trashed', 'trashed')->name('customers.trashed');
        Route::get('customers/restore/{id}', 'restore')->name('customers.restore');
        Route::delete('customers/forceDelete/{id}', 'forceDelete')->name('customers.forceDelete');
    });
    Route::controller(TestimonialController::class)->group(function () {
        Route::get('testimonials/trashed', 'trashed')->name('testimonials.trashed');
        Route::get('testimonials/restore/{id}', 'restore')->name('testimonials.restore');
        Route::delete('testimonials/forceDelete/{id}', 'forceDelete')->name('testimonials.forceDelete');
    });
    Route::controller(RecentViewProductController::class)->group(function () {
        Route::get('recent_view_products/trashed', 'trashed')->name('recent_view_products.trashed');
        Route::get('recent_view_products/restore/{id}', 'restore')->name('recent_view_products.restore');
    });
    Route::controller(SliderController::class)->group(function () {
        Route::get('sliders/trashed', 'trashed')->name('sliders.trashed');
        Route::get('sliders/restore/{id}', 'restore')->name('sliders.restore');
    });
    Route::prefix('blogs')->controller(BlogController::class)->group(function () {
        Route::get('trashed', 'trashed')->name('blogs.trashed');
        Route::get('restore/{id}', 'restore')->name('blogs.restore');
    }); 
    Route::prefix('quote_requests')->controller(QuoteRequestController::class)->group(function () {
        Route::get('trashed', 'trashed')->name('quote_requests.trashed');
        Route::get('restore/{id}', 'restore')->name('quote_requests.restore');
    });   
    Route::prefix('policies')->controller(PolicyController::class)->group(function () {
        Route::get('trashed', 'trashed')->name('policies.trashed');
        Route::get('restore/{id}', 'restore')->name('policies.restore');
    });  
    Route::prefix('contact_messages')->controller(ContactMessageController::class)->group(function () {
        Route::get('trashed', 'trashed')->name('contact_messages.trashed');
        Route::get('restore/{id}', 'restore')->name('contact_messages.restore');
    });  
    Route::prefix('wishlists')->controller(WishlistController::class)->group(function () {
        Route::get('trashed', 'trashed')->name('wishlists.trashed');
        Route::get('restore/{id}', 'restore')->name('wishlists.restore');
    });  
    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('trashed', 'trashed')->name('users.trashed');
        Route::get('restore/{id}', 'restore')->name('users.restore');
    });  

    //Resource Routes.
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('menu_fields', MenuFieldController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('currencies', CurrencyController::class);
    Route::resource('shipping_methods', ShippingMethodController::class);
    Route::resource('attributes', AttributeController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('question_answers', QuestionAnswerController::class);
    Route::resource('coupons', CouponController::class);
    Route::resource('flash_deals', FlashDealController::class);
    Route::resource('tags', TagController::class);
    Route::resource('payment_methods', PaymentMethodController::class);
    Route::resource('products', ProductController::class);
    Route::resource('units', UnitController::class);
    Route::resource('tax_types', TaxTypeController::class);
    Route::resource('payment_modes', PaymentModeController::class);
    Route::resource('payment_types', PaymentTypeController::class);
    Route::resource('product_conditions', ProductConditionController::class);
    Route::resource('subscribers', SubscriberController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('recent_view_products', RecentViewProductController::class);
    Route::resource('sliders', SliderController::class);
    Route::resource('blogs', BlogController::class);
    Route::resource('quote_requests', QuoteRequestController::class);
    Route::resource('policies', PolicyController::class);
    Route::resource('contact_messages', ContactMessageController::class);
    Route::resource('wishlists', WishlistController::class);
    Route::resource('users', UserController::class);
});