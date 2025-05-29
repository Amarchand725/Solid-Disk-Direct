<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\ProductController;
use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $trail = [];
        
        $category = $this->resource;
        if ($category) {
            $trail = app(ProductController::class)->getCategoryTrailFromRelations($category);
        }
        return [
            "id"  => $this->id ?? '',
            "banner" => $this->banner ? asset(Storage::url($this->banner))  : asset(Storage::url('images/default.png')),
            "name"  => $this->name ?? '',
            "slug"  => $this->slug ?? '',
            'category_trail' => $trail,
            'category_url' => implode('/', array_column($trail, 'slug')),
            "description" => $this->description ?? '',
            "is_featured" => $this->is_featured ?? '',
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'limited_products' => ProductResource::collection($this->whenLoaded('limitedProducts')),
            'children_recursive' => self::collection($this->whenLoaded('childrenRecursive')),      
        ]; 
    }
}
