<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id ?? '',
            "logo" => $this->logo ? asset(Storage::url($this->logo))  : '',
            "banner" => $this->banner ? asset(Storage::url($this->banner))  : '',
            "name"  => $this->name ?? '',
            "slug" => $this->slug ?? '',
            "description" => $this->description ?? '',
            "is_featured" => $this->is_featured ? 'Featured' : '',
            // 'products' => ProductResource::collection($this->whenLoaded('hasProducts')),
            'products' => ProductResource::collection($this->hasProducts),
        ];  
    }
}
