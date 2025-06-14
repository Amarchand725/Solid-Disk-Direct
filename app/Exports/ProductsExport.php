<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Controllers\Api\ProductController;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $products = Product::with(['hasBrand', 'hasProductCondition'])
        ->where('status', 1)
        ->whereNotNull('thumbnail')
        ->where('unit_price', '>=', 100)
        ->orderBy('id')
        ->skip(1000) // 1 - 1
        ->take(50000)
        ->get()
        ->filter(function ($product) {
            // Check if the file exists in storage/app/public/
            return Storage::disk('public')->exists($product->thumbnail);
        })
        ->values(); // Re-index after filtering
    
        return $products;
    }

    public function headings(): array
    {
        // 'google_product_category',
        return [
            'ID', 'Title', 'Description', 'Link', 'Image Link', 'Availability', 'Price', 'Product Type',
            'Brand', 'MPN', 'Condition', 'Product Weight', 'Shipping Weight', 'Availability Date'
        ];
    }

    public function map($product): array
    {
        $trail = [];

        if ($product->mainCategory) {
            $trail = app(ProductController::class)
                        ->getCategoryTrailFromRelations($product->mainCategory);
        }

        $categoryPath = implode(' > ', array_column($trail, 'name'));

        $categorySlugPath = implode('/', array_column($trail, 'slug'));

        $productUrl = 'https://soliddiskdirect.com/products/' . $categorySlugPath . '/' . $product->slug;

        return [
            $product->id ?? '-',
            $product->title ?? '-',
            strip_tags($product->short_description) ?? '-',
            $productUrl,
            asset(Storage::url($product->thumbnail)) ?? '-',
            'in stock',
            'USD ' . number_format($product->unit_price, 2) ?? '-',
            optional($product->mainCategory)->name ?? '-',
            optional($product->hasBrand)->name ?? '-',
            $product->mpn ?? '-',
            optional($product->hasProductCondition)->name ?? '-',
            '5 lbs',
            '5 lbs',
            now()->addYear()->toIso8601String(),
        ];
    }
}