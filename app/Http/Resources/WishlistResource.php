<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"  => $this->id ?? '',
            // "customer"  => $this->hasCustomer ? new CustomerResource($this->hasCustomer) : '',
            "product"  => $this->hasProducts ? ProductResource::collection($this->hasProducts) : '',
        ]; 
    }
}
