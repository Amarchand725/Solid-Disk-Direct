<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class WebController extends Controller
{
    public function SddGoogleShopping(){
        return Excel::download(new ProductsExport, 'sdd-google-shopping.csv');

        // $csvHeader = ['id', 'title', 'description', 'link', 'image_link', 'availability', 'price', 'google_product_category',
        //             'brand', 'mpn', 'condition', 'product_weight', 'shipping_weight', 'availability_date'];
        
        // $response = response()->stream(function () use ($csvHeader) {
        //     $handle = fopen('php://output', 'w');
        //     fputcsv($handle, $csvHeader);

        //     Product::where('status', 1)
        //         ->whereNotNull('thumbnail')
        //         ->where('unit_price', '>=', 100)
        //         ->chunk(10, function ($products) use ($handle) {
        //             foreach ($products as $product) {
        //                 if (!empty($product->unit_price) && is_numeric($product->unit_price) && $product->unit_price > 0) {
        //                     // $category = isset($product->category) ? $product->category->default_name: '';
        //                     // $sub_category = isset($product->subcategory) ? $product->subcategory->default_name: '';
        //                     // $sub_sub_category = isset($product->subsubcategory) ? $product->subsubcategory->default_name : '';
                            
        //                     // if(isset($sub_category) && !empty($sub_category)){
        //                     //     $category = $category.' > ';
        //                     // }

        //                     // if(isset($sub_sub_category) && !empty($sub_sub_category)){
        //                     //     $sub_category = $sub_category.' > ';
        //                     // }

        //                     // $google_product_category = $category.' '.$sub_category.' '.$sub_sub_category;

        //                     $brand = isset($product->brand) ? $product->brand->name : '';
        //                     $condition = isset($product->hasProductCondition) ? $product->hasProductCondition->name : '';

        //                     fputcsv($handle, [
        //                         $product->id,
        //                         $product->name,
        //                         $product->meta_description,
        //                         url('/products/' . $product->slug),
        //                         asset('storage/app/public/products/thumbnail').'/'.$product->thumbnail,
        //                         'In Stock',
        //                         'USD '.$product->unit_price,
        //                         // $google_product_category,
        //                         // $product->product_type,
        //                         $brand,
        //                         $product->mpn,
        //                         $condition,
        //                         '5 lbs', //product_weight
        //                         '5 lbs', //shipping_weight
        //                         '2026-02-24T00:30-0800', //availability date
        //                     ]);
        //                 }
        //             }
        //         });

        //     fclose($handle);
        // }, 200, [
        //     'Content-Type' => 'text/csv',
        //     'Content-Disposition' => 'attachment; filename="sdd-google-shopping.csv"',
        // ]);

        // return $response;

    }
}
