<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductImage;
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

        $category = Category::firstOrCreate([
            'name' => $row['product_type'],
        ], [
            'description' => $row['product_type'],
        ]);

        $detectedBrand = $this->detectOrCreateBrand($row['description']);

        if (empty($model)) {
            $product = new Product([
                'created_by' => Auth::user()->id,
                'thumbnail' => !empty($row['image_link']) ? 'uploads/products/' . $row['image_link'] : null,
                'title' => $row['title'] ?? null,
                'sku' => $row['sku'] ?? null,
                'brand' => $detectedBrand?->id,
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
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = !empty($row['image_link']) ? 'uploads/products/additional_images/' . $row['image_link'] : null;
                $productImage->save();
            }

            return $product;
        }

        return null;
    }

    function detectOrCreateBrand($productTitle)
    {
        $brands = Brand::all(); // Load all brands

        foreach ($brands as $brand) {
            if (Str::startsWith(strtolower($productTitle), strtolower($brand->name))) {
                return $brand; // Return the matched brand model
            }
        }

        // If no brand matched, extract the first word as a potential brand
        $potentialBrand = Str::before($productTitle, ' ');
        $potentialBrand = ucfirst(strtolower($potentialBrand)); // Normalize it

        // Create the brand and return it
        return Brand::create([
                    'name' => $potentialBrand,
                    'description' => $potentialBrand
                ]);
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

