<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\ProductController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $trail = [];

        if ($this->mainCategory) {
            $trail = app(ProductController::class)->getCategoryTrailFromRelations($this->mainCategory);
        }

        $thumbnailImage = [
            'image' => $this->thumbnail ? asset(Storage::url($this->thumbnail))  : '', // ✅ match the key used in Vue
        ];

        return [
            "id"  => $this->id ?? '',
            "thumbnail" => $this->thumbnail ? asset(Storage::url($this->thumbnail))  : '',
            "min_quantity"  => $this->min_quantity ?? '',
            "mpn"  => $this->mpn ?? '',
            "sku"  => $this->sku ?? '',
            "title"  => $this->title ?? '',
            "slug" => $this->slug ?? '',
            'category_trail' => $trail,
            'category_url' => implode('/', array_column($trail, 'slug')),
            "unit_price" => $this->unit_price ?? '',
            "discount_price" => $this->discount_price ?? '',
            "short_description" => $this->short_description ?? '',
            "full_description" => $this->full_description ?? '',
            'category' => new CategoryResource($this->whenLoaded('mainCategory')),
            'brand' => new BrandResource($this->whenLoaded('hasBrand')),
            'condition' => new ProductConditionResource($this->whenLoaded('hasProductCondition')),
            'unit' => new UnitResource($this->whenLoaded('hasUnit')),
            // 'images' => ProductImageResource::collection($this->hasProductImages),
            'images' => collect([$thumbnailImage])->merge(ProductImageResource::collection($this->hasProductImages)),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];        
    }
}
