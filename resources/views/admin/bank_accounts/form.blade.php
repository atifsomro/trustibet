@php
    $isEdit = isset($bankAccount);
@endphp


<div class="row">


    {{-- Title --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Account Title
        </label>

        <input type="text"
               name="title"
               class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $bankAccount->title ?? '') }}"
               placeholder="e.g. Meezan Bank PKR">

        @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>



    {{-- Bank Name --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Bank Name
        </label>

        <input type="text"
               name="bank_name"
               class="form-control @error('bank_name') is-invalid @enderror"
               value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}"
               placeholder="e.g. Meezan Bank">

        @error('bank_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>




    {{-- Account Holder --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Account Holder Name
        </label>

        <input type="text"
               name="account_title"
               class="form-control @error('account_title') is-invalid @enderror"
               value="{{ old('account_title', $bankAccount->account_title ?? '') }}">

        @error('account_title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>




    {{-- Account Number --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Account Number
        </label>

        <input type="text"
               name="account_number"
               class="form-control @error('account_number') is-invalid @enderror"
               value="{{ old('account_number', $bankAccount->account_number ?? '') }}">

        @error('account_number')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>




    {{-- IBAN --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            IBAN
        </label>

        <input type="text"
               name="iban"
               class="form-control"
               value="{{ old('iban', $bankAccount->iban ?? '') }}">

    </div>




    {{-- Swift --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            SWIFT Code
        </label>

        <input type="text"
               name="swift_code"
               class="form-control"
               value="{{ old('swift_code', $bankAccount->swift_code ?? '') }}">

    </div>




    {{-- Branch --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Branch Name
        </label>

        <input type="text"
               name="branch_name"
               class="form-control"
               value="{{ old('branch_name', $bankAccount->branch_name ?? '') }}">

    </div>




    {{-- Branch Code --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Branch Code
        </label>

        <input type="text"
               name="branch_code"
               class="form-control"
               value="{{ old('branch_code', $bankAccount->branch_code ?? '') }}">

    </div>




    {{-- Currency --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Currency
        </label>

        <input type="text"
               name="currency"
               class="form-control"
               value="{{ old('currency', $bankAccount->currency ?? 'PKR') }}">

    </div>




    {{-- Type --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Payment Type
        </label>


        <select name="type"
                class="form-control">


            @foreach([
                'bank' => 'Bank',
                'jazzcash' => 'JazzCash',
                'easypaisa' => 'EasyPaisa',
                'other' => 'Other'
            ] as $key => $value)

                <option value="{{ $key }}"
                    {{ old('type', $bankAccount->type ?? 'bank') == $key ? 'selected' : '' }}>

                    {{ $value }}

                </option>

            @endforeach


        </select>

    </div>




    {{-- Sort Order --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Sort Order
        </label>

        <input type="number"
               name="sort_order"
               class="form-control"
               value="{{ old('sort_order', $bankAccount->sort_order ?? 0) }}">

    </div>
    {{-- Conversion Rate --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Conversion Rate (PKR per 1 USD)
        </label>

        <input type="number"
               name="conversion_rate"
               class="form-control"
               step="0.01"
               min="0"
               value="{{ old('conversion_rate', $bankAccount->conversion_rate ?? 0) }}">
        <small class="text-muted">
            Used to show how much PKR the user must pay for the USD deposit amount.
        </small>

    </div>




    {{-- QR Code --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Picture
        </label>


        <input type="file"
               name="picture"
               class="form-control">


        @if($isEdit && $bankAccount->picture)

            <div class="mt-2">

                <img src="{{ $bankAccount->picture_url }}"
                     width="120"
                     class="border rounded"
                     alt="{{ $bankAccount->bank_name }}">

            </div>

        @endif


    </div>
    {{-- QR Code --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            QR Code
        </label>


        <input type="file"
               name="qr_code"
               class="form-control">


        @if($isEdit && $bankAccount->qr_code)

            <div class="mt-2">

                <img src="{{ $bankAccount->qr_code_url }}"
                     width="120"
                     class="border rounded"
                     alt="{{ $bankAccount->bank_name }} QR">

            </div>

        @endif


    </div>




    {{-- Instructions --}}
    <div class="col-md-12 mb-3">

        <label class="form-label">
            Instructions
        </label>


        <textarea name="instructions"
                  rows="4"
                  class="form-control">{{ old('instructions', $bankAccount->instructions ?? '') }}</textarea>

    </div>




    {{-- Status --}}
    <div class="col-md-12 mb-3">


        <div class="form-check">

            <input type="checkbox"
                   name="is_active"
                   value="1"
                   class="form-check-input"
                   id="activeStatus"

                   {{ old('is_active', $bankAccount->is_active ?? true) ? 'checked' : '' }}>


            <label class="form-check-label"
                   for="activeStatus">

                Active Account

            </label>

        </div>


    </div>


</div>