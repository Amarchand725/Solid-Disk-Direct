<?php

namespace App\Providers;

use App\Models\Setting;
use App\Observers\ModelLogObserver;
use App\Traits\HasModelLogObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(100);

        $namespace = 'App\\Models\\';
        $path = app_path('Models');

        $models = collect(scandir($path))
            ->filter(function ($file) {
                return preg_match('/\.php$/', $file); // Only PHP files
            })
            ->map(function ($file) use ($namespace) {
                return $namespace . pathinfo($file, PATHINFO_FILENAME);
            });

        foreach ($models as $model) {
            if (class_exists($model) && in_array(HasModelLogObserver::class, class_uses($model))) {
                $model::observe(ModelLogObserver::class);
            }
        }

        View::share('setting', cache()->remember('setting', 60*60, function () {
            return Setting::first();
        }));
    }
}
