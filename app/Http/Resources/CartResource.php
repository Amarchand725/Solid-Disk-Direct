<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            "session_id"  => $this->session_id ?? '',
            "status"  => $this->status ?? '',
            "subtotal"  => $this->subtotal ?? '',
            "discount_total"  => $this->discount_total ?? '',
            "shipping_weight"  => $this->shipping_weight ?? 0,
            "shipping_cost"  => $this->shipping_cost ?? '',
            "tax_rate"  => $this->tax_rate ?? '',
            "tax_amount"  => $this->tax_amount ?? '',
            "total"  => $this->total ?? '',
            "items"  => $this->items ? CartItemResource::collection($this->items) : null ,
        ]; 
    }
}
