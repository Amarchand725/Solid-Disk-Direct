<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductLine;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public $importedCount = 0;

    public function model(array $row)
    {
        $model = Product::where('title', $row['title'])->first();

        $category = Category::firstOrCreate([
            'name' => $row['product_type'],
        ], [
            'description' => $row['product_type'],
        ]);

        $detected = $this->detectOrCreateBrandAndProductLine($row['description']);
        $brandId = $detected['brand']->id ?? null;

        if (empty($model)) {
            $product = new Product([
                'created_by' => Auth::user()->id,
                'thumbnail' => !empty($row['image_link']) ? 'uploads/products/' . $row['image_link'] : null,
                'title' => $row['title'] ?? null,
                'sku' => $row['sku'] ?? null,
                'brand' => $brandId,
                'category' => $category->id,
                'stock_quantity' => $row['stock_quantity'] ?? null,
                'min_quantity' => $row['min_quantity'] ?? null,
                'short_description' => $row['description'] ?? null,
                'full_description' => $row['full_description'] ?? null,
                'unit_price' => $row['price'] ?? null,
                'mpn' => $row['mpn'] ?? null,
                'discount_price' => $row['discount_price'] ?? null,
                'is_featured' => $row['is_featured'] ?? null,
                'is_refundable' => $row['is_refundable'] ?? null,
                'unit' => $row['unit'] ?? null,
                'tax_type' => $row['tax_type'] ?? null,
                'tax_mode' => $row['tax_mode'] ?? null,
                'discount_type' => $row['discount_type'] ?? null,
                'condition' => $row['condition'] ?? null,
                'product_weight' => $row['product_weight'] ?? null,
                'shipping_weight' => $row['shipping_weight'] ?? null,
            ]);

            $product->save();

            // Get all parent categories
            $allCategoryIds = [$category->id];
            if ($category) {
                $parentCategoryIds = $this->getAllParentCategoryIds($category);
                $allCategoryIds = array_unique(array_merge($parentCategoryIds, [$category->id]));
            }

            if (!empty($allCategoryIds)) {
                $product->categories()->syncWithoutDetaching($allCategoryIds);
            }

            if($product){
                if(!empty($detected['product_line'])){
                    $productLine = new ProductLine();
                    $productLine->name = $detected['product_line'] ?? null;
                    $productLine->save();
                }
            }

            $this->importedCount++;
            return $product;
        }

        return null;
    }

    function detectOrCreateBrandAndProductLine($productTitle)
    {
        $brands = Brand::all(); // Load all brands

        foreach ($brands as $brand) {
            if (Str::startsWith(strtolower($productTitle), strtolower($brand->name))) {
                $productLine = trim(Str::replaceFirst($brand->name, '', $productTitle));
                return [
                    'brand' => $brand,
                    'product_line' => $productLine
                ];
            }
        }

        // If no brand matched, extract the first word as a potential brand
        $potentialBrandName = Str::before($productTitle, ' ');
        $potentialBrandName = ucfirst(strtolower($potentialBrandName));

        // Create the brand
        $newBrand = Brand::create([
            'name' => $potentialBrandName,
            'description' => $potentialBrandName
        ]);

        // Product line is everything after the first word
        $productLine = trim(Str::after($productTitle, $potentialBrandName));

        return [
            'brand' => $newBrand,
            'product_line' => $productLine
        ];
    }

    private function getAllParentCategoryIds(Category $category, &$collected = [])
    {
        foreach ($category->parents as $parent) {
            if (!in_array($parent->id, $collected)) {
                $collected[] = $parent->id;
                $this->getAllParentCategoryIds($parent, $collected);
            }
        }
        return $collected;
    }
}

