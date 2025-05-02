<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            "title"  => $this->title ?? '',
            "slug"  => $this->slug ?? '',
            "description" => $this->description ?? '',
            "created_at" => $this->created_at ? $this->created_at->diffForHumans() : '',
        ]; 
    }
}
