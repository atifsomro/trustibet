<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyLotteryTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Please select the number of tickets.',
            'quantity.integer' => 'Ticket quantity must be a valid number.',
            'quantity.min' => 'You must purchase at least one ticket.',
        ];
    }
}