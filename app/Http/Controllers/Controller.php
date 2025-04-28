<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\UploadsImages;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, UploadsImages;

    protected array $permissions = []; // Permission array, set in child classes

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $routeAction = $request->route()?->getActionMethod(); // Get function name

            if ($routeAction && isset($this->permissions[$routeAction])) {
                $this->authorize($this->permissions[$routeAction]); // Dynamic authorization
            }

            return $next($request);
        });
    }
}
