@php
    $isEdit = isset($lottery);
@endphp

<div class="row">

    {{-- Lottery Title --}}
    <div class="col-md-8 mb-3">
        <label class="form-label">
            Lottery Title <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="title"
            class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $lottery->title ?? '') }}"
            placeholder="e.g. Lucky Draw"
        >

        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Ticket Price --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Ticket Price <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="ticket_price"
            step="0.01"
            min="0.01"
            class="form-control @error('ticket_price') is-invalid @enderror"
            value="{{ old('ticket_price', $lottery->ticket_price ?? '') }}"
            placeholder="1.00"
        >

        @error('ticket_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Maximum Tickets --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Maximum Tickets
        </label>

        <input
            type="number"
            name="max_tickets"
            min="1"
            class="form-control @error('max_tickets') is-invalid @enderror"
            value="{{ old('max_tickets', $lottery->max_tickets ?? '') }}"
            placeholder="Leave empty for unlimited"
        >

        @error('max_tickets')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Sales Start --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            Sales Start
        </label>

        <input
            type="datetime-local"
            name="sales_start_at"
            class="form-control @error('sales_start_at') is-invalid @enderror"
            value="{{ old(
                'sales_start_at',
                isset($lottery->sales_start_at)
                    ? $lottery->sales_start_at->format('Y-m-d\TH:i')
                    : ''
            ) }}"
        >

        @error('sales_start_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Sales Close --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            Sales Close <span class="text-danger">*</span>
        </label>

        <input
            type="datetime-local"
            name="sales_end_at"
            class="form-control @error('sales_end_at') is-invalid @enderror"
            value="{{ old(
                'sales_end_at',
                isset($lottery->sales_end_at)
                    ? $lottery->sales_end_at->format('Y-m-d\TH:i')
                    : ''
            ) }}"
        >

        @error('sales_end_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Draw Date --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            Draw Date/Time
        </label>

        <input
            type="datetime-local"
            name="draw_at"
            class="form-control @error('draw_at') is-invalid @enderror"
            value="{{ old(
                'draw_at',
                isset($lottery->draw_at)
                    ? $lottery->draw_at->format('Y-m-d\TH:i')
                    : ''
            ) }}"
        >

        @error('draw_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Prize Configuration --}}
    <div class="col-12">
        <hr>

        <h5 class="mb-1">
            Prize Configuration
        </h5>

        <p class="text-muted mb-3">
            Each prize category has exactly one winner.
        </p>
    </div>


    {{-- First Prize --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            1st Prize <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="first_prize"
            step="0.01"
            min="0"
            class="form-control @error('first_prize') is-invalid @enderror"
            value="{{ old('first_prize', $lottery->first_prize ?? '') }}"
            placeholder="100.00"
        >

        @error('first_prize')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            1 winner
        </small>
    </div>


    {{-- Second Prize --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            2nd Prize <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="second_prize"
            step="0.01"
            min="0"
            class="form-control @error('second_prize') is-invalid @enderror"
            value="{{ old('second_prize', $lottery->second_prize ?? '') }}"
            placeholder="50.00"
        >

        @error('second_prize')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            1 winner
        </small>
    </div>


    {{-- Third Prize --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            3rd Prize <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="third_prize"
            step="0.01"
            min="0"
            class="form-control @error('third_prize') is-invalid @enderror"
            value="{{ old('third_prize', $lottery->third_prize ?? '') }}"
            placeholder="25.00"
        >

        @error('third_prize')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            1 winner
        </small>
    </div>


    {{-- Fourth Prize --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            4th Prize <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="fourth_prize"
            step="0.01"
            min="0"
            class="form-control @error('fourth_prize') is-invalid @enderror"
            value="{{ old('fourth_prize', $lottery->fourth_prize ?? '') }}"
            placeholder="15.00"
        >

        @error('fourth_prize')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            1 winner
        </small>
    </div>


    {{-- Fifth Prize --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            5th Prize <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="fifth_prize"
            step="0.01"
            min="0"
            class="form-control @error('fifth_prize') is-invalid @enderror"
            value="{{ old('fifth_prize', $lottery->fifth_prize ?? '') }}"
            placeholder="10.00"
        >

        @error('fifth_prize')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            1 winner
        </small>
    </div>


    {{-- Sort Order --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            Sort Order
        </label>

        <input
            type="number"
            name="sort_order"
            min="0"
            class="form-control"
            value="{{ old('sort_order', $lottery->sort_order ?? 0) }}"
        >
    </div>


    {{-- Status --}}
    <div class="col-md-8 mb-3">
        <label class="form-label">
            Status <span class="text-danger">*</span>
        </label>

        <select
            name="status"
            class="form-control @error('status') is-invalid @enderror"
        >
            @foreach([
                'draft' => 'Draft',
                'scheduled' => 'Scheduled',
                'selling' => 'Selling',
                'ended' => 'Ended',
                'drawing' => 'Drawing',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ] as $key => $label)

                <option
                    value="{{ $key }}"
                    {{ old(
                        'status',
                        isset($lottery->status)
                            ? $lottery->status->value
                            : 'draft'
                    ) === $key ? 'selected' : '' }}
                >
                    {{ $label }}
                </option>

            @endforeach
        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- Active --}}
    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">

            <input
                type="hidden"
                name="is_active"
                value="0"
            >

            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="form-check-input"
                id="lotteryActive"
                {{ old(
                    'is_active',
                    $lottery->is_active ?? true
                ) ? 'checked' : '' }}
            >

            <label
                class="form-check-label"
                for="lotteryActive"
            >
                Active Lottery
            </label>

        </div>
    </div>


    {{-- Description --}}
    <div class="col-md-12 mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea
            name="description"
            rows="4"
            class="form-control"
            placeholder="Optional lottery instructions/details"
        >{{ old('description', $lottery->description ?? '') }}</textarea>

    </div>

</div>