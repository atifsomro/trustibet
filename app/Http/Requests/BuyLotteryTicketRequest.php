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
        return [];
    }
}
