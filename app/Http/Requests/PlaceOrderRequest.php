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
            // 'payment' => 'required|string',
            // 'payment' => 'required|array',
            // 'payment.method' => 'required|string|in:paypal,payarc,stripe',
            // 'payment.payment_method_id' => 'required|string',

            // Shipping fields (always required)
            'shipping.email' => ['required', 'email'],
            'shipping.first_name' => ['required', 'string', 'max:255'],
            'shipping.last_name' => ['required', 'string', 'max:255'],
            'shipping.company' => ['nullable', 'string', 'max:255'],
            'shipping.address' => ['required', 'string', 'max:500'],
            'shipping.shippingCountry' => ['nullable', 'max:100'],
            'shipping.shippingState' => ['nullable', 'max:100'],
            'shipping.shippingCity' => ['nullable', 'max:100'],
            'shipping.zip' => ['required', 'string', 'max:20'],
            'shipping.phone' => ['required', 'string', 'max:20'],

            // Billing fields
            'billing' => ['sometimes', 'array'],
            'billing.same_as_shipping' => ['required', 'boolean'],

            // REVERSED LOGIC: required if billing.same_as_shipping == true
            'billing.email' => ['required_if:billing.same_as_shipping,true', 'email'],
            'billing.first_name' => ['required_if:billing.same_as_shipping,true', 'string', 'max:255'],
            'billing.last_name' => ['required_if:billing.same_as_shipping,true', 'string', 'max:255'],
            'billing.company' => ['nullable', 'string', 'max:255'],
            'billing.address' => ['required_if:billing.same_as_shipping,true', 'string', 'max:500'],
            'billing.billingCountry' => ['nullable', 'max:100'],
            'billing.billingState' => ['nullable', 'max:100'],
            'billing.billingCity' => ['nullable', 'max:100'],
            'billing.zip' => ['required_if:billing.same_as_shipping,true', 'string', 'max:20'],
            'billing.phone' => ['required_if:billing.same_as_shipping,true', 'string', 'max:20'],
        ];

        // return [
        //     'shipping' => 'required|array',
        //     'shipping.first_name' => 'required|string',
        //     'shipping.last_name' => 'required|string',
        //     'shipping.email' => 'required|email',
        //     'shipping.phone' => 'required|string',
        //     'shipping.address' => 'required|string',
        //     'shipping.city' => 'required|string',
        //     'shipping.state' => 'required|string',
        //     'shipping.zip' => 'required|string',
        //     'shipping.country' => 'required|string',
    
        //     'billing.same_as_shipping' => 'required|boolean',
        //     'billing.first_name' => 'required_if:billing.same_as_shipping,false|string',
        //     'billing.last_name' => 'required_if:billing.same_as_shipping,false|string',
        //     'billing.email' => 'required_if:billing.same_as_shipping,false|email',
        //     'billing.phone' => 'required_if:billing.same_as_shipping,false|string',
        //     'billing.address' => 'required_if:billing.same_as_shipping,false|string',
        //     'billing.city' => 'required_if:billing.same_as_shipping,false|string',
        //     'billing.state' => 'required_if:billing.same_as_shipping,false|string',
        //     'billing.zip' => 'required_if:billing.same_as_shipping,false|string',
        //     'billing.country' => 'required_if:billing.same_as_shipping,false|string',
    
        //     // 'shipping_method_id' => 'required|exists:shipping_methods,id',
    
        //     // 'payment.method' => 'required|in:stripe',
        //     // 'payment.stripe_token' => 'required_if:payment.method,stripe',
        // ];
    }
}
