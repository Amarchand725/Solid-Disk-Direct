<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "image" => $this->image ? asset(Storage::url($this->image))  : '',
            "name"  => $this->name ?? '',
            "email"  => $this->email ?? '',
            "phone"  => $this->phone ?? '',
        ];   
    }
}
