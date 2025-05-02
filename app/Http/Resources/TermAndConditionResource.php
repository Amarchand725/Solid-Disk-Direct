<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class TermAndConditionResource extends JsonResource
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
            "content" => $this->content ?? '',
        ];  
    }
}
