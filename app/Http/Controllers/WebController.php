<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class WebController extends Controller
{
    public function SddGoogleShopping(){
        return Excel::download(new ProductsExport, 'sdd-google-shopping.csv');
    }
}
