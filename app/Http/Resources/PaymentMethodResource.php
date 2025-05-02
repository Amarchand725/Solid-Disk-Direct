<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "logo" => $this->logo ? asset(Storage::url($this->logo))  : '',
            "name"  => $this->name ?? '',
            "slug"  => $this->slug ?? '',
            "type" => $this->type ?? '',
        ]; 
    }
}
