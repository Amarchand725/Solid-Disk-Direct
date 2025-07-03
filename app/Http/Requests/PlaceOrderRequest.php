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
            'shipping.address_line_2' => ['string', 'max:500'],
            'shipping.shippingCountry' => ['required', 'max:100'],
            'shipping.shippingState' => ['required', 'max:100'],
            'shipping.shippingCity' => ['required', 'max:100'],
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
            'billing.address_line_2' => ['string', 'max:500'],
            'billing.billCountry' => ['required_if:billing.same_as_shipping,true', 'max:100'],
            'billing.billState' => ['required_if:billing.same_as_shipping,true', 'max:100'],
            'billing.billCity' => ['required_if:billing.same_as_shipping,true', 'max:100'],
            'billing.zip' => ['required_if:billing.same_as_shipping,true', 'string', 'max:20'],
            'billing.phone' => ['required_if:billing.same_as_shipping,true', 'string', 'max:20'],
        ];
    }
}
