<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallet;

use App\Services\Wallet\WalletManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RequestWithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'min:1',
            ],

            'payment_method' => [
                'required',
                'string',
            ],

            'account_details' => [
                'required',
                'string',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var WalletManager $walletManager */
            $walletManager = app(WalletManager::class);

            $wallet = $walletManager->getOrCreate($this->user());

            if ((float) $this->amount > (float) $wallet->withdrawable_balance) {
                $validator->errors()->add(
                    'amount',
                    'Insufficient withdrawable balance.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Withdrawal amount is required.',
            'amount.min' => 'Withdrawal amount must be greater than zero.',

            'payment_method.required' => 'Please select a payment method.',

            'account_details.required' => 'Account details are required.',
            'account_details.string' => 'Invalid account details.',

            'remarks.max' => 'Remarks may not exceed 500 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => trim($this->amount),
            'payment_method' => trim((string) $this->payment_method),
            'account_details' => trim((string) $this->account_details),
            'remarks' => $this->remarks
                ? trim((string) $this->remarks)
                : null,
        ]);
    }
}