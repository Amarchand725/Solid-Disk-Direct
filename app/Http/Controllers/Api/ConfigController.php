<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductLine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ConfigController extends Controller
{
    public function getBrands()
    {
        return Brand::select('id', 'name')->get();
    }

    public function getCategories()
    {
        return Category::select('id', 'name')->get();
    }

    public function getProductLines(Request $request)
    {
        $categoryId = $request->category_id;

        $productLineIds = Product::whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            })
            ->whereNotNull('product_line_id')
            ->pluck('product_line_id')
            ->unique();

        return ProductLine::whereIn('id', $productLineIds)
            ->select('id', 'name')
            ->get();
    }

    public function getProducts(Request $request)
    {
        $models = Product::where('product_line_id', $request->product_line_id)->where('status', 1)->get();

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => ProductResource::collection($models)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }
}
