<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class ProductAction extends Component
{
    public $model;
    public $singularLabel;
    public $routePrefix;

    /**
     * Create a new component instance.
     */
    public function __construct($model, $singularLabel, $routePrefix)
    {
        $this->model = $model;
        $this->singularLabel = $singularLabel;
        $this->routePrefix = $routePrefix;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $cacheKey = 'product-action-' . $this->model->id;

        // Cache for a reasonable amount of time
        return Cache::remember($cacheKey, now()->addMinutes(10), function () {
            return view('components.product-action');
        });
    }
}
