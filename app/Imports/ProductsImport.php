<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = Product::where('title', $row['title'])->first();
        if(empty($model)){
            return new Product([
                'created_by' => Auth::user()->id,
                'thumbnail' => $row['thumbnail'] ?? NULL,
                'title' => $row['title'] ?? NULL,
                'sku' => $row['sku'] ?? NULL,
                'brand' => $row['brand'] ?? NULL,
                'category' => $row['category'] ?? NULL,
                'stock_quantity' => $row['stock_quantity'] ?? NULL,
                'min_quantity' => $row['min_quantity'] ?? NULL,
                'short_description' => $row['short_description'] ?? NULL,
                'full_description' => $row['full_description'] ?? NULL,
                'unit_price' => $row['unit_price'] ?? NULL,
                'discount_price' => $row['discount_price'] ?? NULL,
                'is_featured' => $row['is_featured'] ?? NULL,
                'is_refundable' => $row['is_refundable'] ?? NULL,
                'unit' => $row['unit'] ?? NULL,
                'tax_type' => $row['tax_type'] ?? NULL,
                'tax_mode' => $row['tax_mode'] ?? NULL,
                'discount_type' => $row['discount_type'] ?? NULL,
                'condition' => $row['condition'] ?? NULL,
            ]);
        }
    }
}

