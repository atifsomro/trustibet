<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallet;

use App\Services\Wallet\WalletManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RequestWithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'payment_method' => [
                'required',
                'string',
                'max:100',
            ],

            'account_details' => [
                'required',
                'array',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Configure the validator.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            if ($validator->fails()) {
                return;
            }

            /** @var WalletManager $walletManager */
            $walletManager = app(WalletManager::class);

            $wallet = $walletManager->getOrCreate($this->user());

            if ((int) $this->amount > $wallet->withdrawable_balance) {
                $validator->errors()->add(
                    'amount',
                    'Insufficient withdrawable balance.'
                );
            }
        });
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Withdrawal amount is required.',
            'amount.integer' => 'Withdrawal amount must be a whole number.',
            'amount.min' => 'Withdrawal amount must be greater than zero.',

            'payment_method.required' => 'Please select a payment method.',
            'payment_method.max' => 'Payment method is too long.',

            'account_details.required' => 'Account details are required.',
            'account_details.array' => 'Invalid account details.',

            'remarks.max' => 'Remarks may not exceed 500 characters.',
        ];
    }

    /**
     * Sanitise input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => (int) $this->amount,
            'payment_method' => trim((string) $this->payment_method),
            'remarks' => $this->remarks
                ? trim((string) $this->remarks)
                : null,
        ]);
    }
}