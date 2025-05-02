<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping' => 'required|array',
            'shipping.first_name' => 'required|string',
            'shipping.last_name' => 'required|string',
            'shipping.email' => 'required|email',
            'shipping.phone' => 'required|string',
            'shipping.address' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.state' => 'required|string',
            'shipping.zip' => 'required|string',
            'shipping.country' => 'required|string',
    
            'billing.same_as_shipping' => 'required|boolean',
            'billing.first_name' => 'required_if:billing.same_as_shipping,false|string',
            'billing.last_name' => 'required_if:billing.same_as_shipping,false|string',
            'billing.email' => 'required_if:billing.same_as_shipping,false|email',
            'billing.phone' => 'required_if:billing.same_as_shipping,false|string',
            'billing.address' => 'required_if:billing.same_as_shipping,false|string',
            'billing.city' => 'required_if:billing.same_as_shipping,false|string',
            'billing.state' => 'required_if:billing.same_as_shipping,false|string',
            'billing.zip' => 'required_if:billing.same_as_shipping,false|string',
            'billing.country' => 'required_if:billing.same_as_shipping,false|string',
    
            // 'shipping_method_id' => 'required|exists:shipping_methods,id',
    
            // 'payment.method' => 'required|in:stripe',
            // 'payment.stripe_token' => 'required_if:payment.method,stripe',
        ];
    }
}
