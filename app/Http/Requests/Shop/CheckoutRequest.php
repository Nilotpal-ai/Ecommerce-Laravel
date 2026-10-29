<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_name'          => ['required', 'string', 'max:255'],
            'shipping_email'         => ['required', 'email', 'max:255'],
            'shipping_phone'         => ['required', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'shipping_address_line1' => ['required', 'string', 'max:255'],
            'shipping_address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city'          => ['required', 'string', 'max:100'],
            'shipping_state'         => ['required', 'string', 'max:100'],
            'shipping_postal_code'   => ['required', 'string', 'max:20'],
            'shipping_country'       => ['required', 'string', 'size:2'],
            'payment_method'         => ['required', 'string', 'in:cod,stripe'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_phone.regex' => 'Please provide a valid contact phone number.',
            'shipping_country.size' => 'Country code must be a 2-character ISO code (e.g., IN, US).',
        ];
    }
}