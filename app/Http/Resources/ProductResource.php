<?php

namespace App\Http\Resources;

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
        // return parent::toArray($request);
        return [
            "thumbnail" => $this->thumbnail ? asset(Storage::url($this->thumbnail))  : '',
            "title"  => $this->title ?? '',
            "slug" => $this->slug ?? '',
            "unit_price" => $this->unit_price ?? '',
        ];        
    }
}
