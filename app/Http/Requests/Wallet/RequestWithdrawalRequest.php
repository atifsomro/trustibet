<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallet;

use App\Services\Wallet\WalletManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
                Rule::in(['bank_transfer', 'jazzcash', 'easypaisa', 'crypto']),
            ],

            'account_title' => [
                'required',
                'string',
                'max:255',
            ],

            'account_number' => [
                'required',
                'string',
                'max:255',
            ],

            // Only required when Bank Transfer is the selected payment method.
            'bank_name' => [
                'required_if:payment_method,bank_transfer',
                'nullable',
                'string',
                'max:255',
            ],

            // Only required when Crypto is the selected payment method.
            'crypto_source' => [
                'required_if:payment_method,crypto',
                'nullable',
                'string',
                'max:255',
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
            'payment_method.in' => 'Please select a valid payment method.',

            'account_title.required' => 'Account title is required.',
            'account_title.max' => 'Account title may not exceed 255 characters.',

            'account_number.required' => 'Account number is required.',
            'account_number.max' => 'Account number may not exceed 255 characters.',

            'bank_name.required_if' => 'Bank name is required for bank transfers.',
            'crypto_source.required_if' => 'Please specify the crypto exchange or wallet source.',

            'remarks.max' => 'Remarks may not exceed 500 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => trim((string) $this->amount),
            'payment_method' => trim((string) $this->payment_method),
            'account_title' => trim((string) $this->account_title),
            'account_number' => trim((string) $this->account_number),
            'bank_name' => $this->bank_name
                ? trim((string) $this->bank_name)
                : null,
            'crypto_source' => $this->crypto_source
                ? trim((string) $this->crypto_source)
                : null,
            'remarks' => $this->remarks
                ? trim((string) $this->remarks)
                : null,
        ]);
    }
}
