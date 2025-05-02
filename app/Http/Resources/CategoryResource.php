<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        return [
            "banner" => $this->banner ? asset(Storage::url($this->banner))  : '',
            "name"  => $this->name ?? '',
            "slug"  => $this->slug ?? '',
            "description" => $this->description ?? '',
            "is_featured" => $this->is_featured ?? '',
            'children' => self::collection($this->whenLoaded('children')),
        ]; 
    }
}
