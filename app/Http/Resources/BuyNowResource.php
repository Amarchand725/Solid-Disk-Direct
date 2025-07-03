<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyNowResource extends JsonResource
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
            "customer_id"  => $this->customer_id ?? '',
            "quantity"  => $this->quantity ?? '',
            "unit_price"  => $this->unit_price ?? '0.00',
            "shipping_weight"  => $this->shipping_weight ?? 0,
            "shipping_cost"  => $this->shipping_cost ?? '0.00',
            "tax_rate"  => $this->tax_rate ?? '0.00',
            "tax_amount"  => $this->tax_amount ?? '0.00',
            "subtotal"  => $this->subtotal ?? '0.00',
            "total"  => $this->total ?? '0.00',
            "product"  => $this->product ?? '',
        ]; 
    }
}
