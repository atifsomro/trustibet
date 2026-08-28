@php
    $isEdit = isset($lottery);
    $durationParts = $isEdit ? $lottery->durationParts() : ['hours' => 0, 'minutes' => 0, 'seconds' => 0];
    $currentStatus = old(
        'status',
        isset($lottery->status)
            ? $lottery->status->value
            : 'draft'
    );
@endphp

<div class="card">
    <div class="card-header">
        <strong>Details</strong>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-8 mb-3">
                <label>
                    Lottery Title <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    name="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $isEdit ? $lottery->title : '') }}"
                    placeholder="e.g. Lucky Draw"
                >
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>
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
                        <option value="{{ $key }}" {{ $currentStatus === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-12 mb-0">
                <label>Description</label>
                <textarea
                    name="description"
                    rows="3"
                    class="form-control"
                    placeholder="Optional lottery instructions or details"
                >{{ old('description', $isEdit ? $lottery->description : '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>Tickets &amp; Countdown</strong>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-4 mb-3">
                <label>
                    Ticket Price <span class="text-danger">*</span>
                </label>
                <input
                    type="number"
                    name="ticket_price"
                    step="0.01"
                    min="0.01"
                    class="form-control @error('ticket_price') is-invalid @enderror"
                    value="{{ old('ticket_price', $isEdit ? $lottery->ticket_price : '') }}"
                    placeholder="1.00"
                >
                @error('ticket_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>Maximum Tickets</label>
                <input
                    type="number"
                    name="max_tickets"
                    min="1"
                    class="form-control @error('max_tickets') is-invalid @enderror"
                    value="{{ old('max_tickets', $isEdit ? $lottery->max_tickets : '') }}"
                    placeholder="Unlimited"
                >
                @error('max_tickets')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>Max Tickets Per User</label>
                <input
                    type="number"
                    name="max_tickets_per_user"
                    min="1"
                    class="form-control @error('max_tickets_per_user') is-invalid @enderror"
                    value="{{ old('max_tickets_per_user', $isEdit ? $lottery->max_tickets_per_user : '') }}"
                    placeholder="Unlimited"
                >
                @error('max_tickets_per_user')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 mb-2">
                <label class="mb-1">
                    Countdown Duration <span class="text-danger">*</span>
                </label>
                <small class="text-muted d-block">
                    The draw runs automatically when this duration elapses from activation.
                </small>
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>Hours</label>
                <input
                    type="number"
                    name="duration_hours"
                    min="0"
                    class="form-control @error('duration_hours') is-invalid @enderror"
                    value="{{ old('duration_hours', $durationParts['hours']) }}"
                >
                @error('duration_hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>Minutes</label>
                <input
                    type="number"
                    name="duration_minutes"
                    min="0"
                    max="59"
                    class="form-control @error('duration_minutes') is-invalid @enderror"
                    value="{{ old('duration_minutes', $durationParts['minutes']) }}"
                >
                @error('duration_minutes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-4 mb-0">
                <label>Seconds</label>
                <input
                    type="number"
                    name="duration_seconds"
                    min="0"
                    max="59"
                    class="form-control @error('duration_seconds') is-invalid @enderror"
                    value="{{ old('duration_seconds', $durationParts['seconds']) }}"
                >
                @error('duration_seconds')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if ($isEdit && ($lottery->isCompleted() || $lottery->hasEnded()))
                <div class="form-group col-12 mb-0 mt-3">
                    <div class="custom-control custom-checkbox">
                        <input
                            type="checkbox"
                            name="start_new_round"
                            value="1"
                            class="custom-control-input"
                            id="startNewRound"
                            {{ old('start_new_round') ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="startNewRound">
                            Start a new round now
                            <small class="d-block text-muted font-weight-normal">
                                Resets the countdown from this moment.
                            </small>
                        </label>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>Prizes</strong>
        <small class="text-muted ml-1">One winner per place</small>
    </div>
    <div class="card-body">
        <div class="form-row">
            @foreach ([
                'first_prize' => ['label' => '1st Prize', 'placeholder' => '100.00'],
                'second_prize' => ['label' => '2nd Prize', 'placeholder' => '50.00'],
                'third_prize' => ['label' => '3rd Prize', 'placeholder' => '25.00'],
                'fourth_prize' => ['label' => '4th Prize', 'placeholder' => '15.00'],
                'fifth_prize' => ['label' => '5th Prize', 'placeholder' => '10.00'],
            ] as $field => $prize)
                <div class="form-group col-sm-6 col-md-4 mb-3">
                    <label>
                        {{ $prize['label'] }} <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        name="{{ $field }}"
                        step="0.01"
                        min="0"
                        class="form-control @error($field) is-invalid @enderror"
                        value="{{ old($field, $isEdit ? $lottery->{$field} : '') }}"
                        placeholder="{{ $prize['placeholder'] }}"
                    >
                    @error($field)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <div class="form-group col-12 mb-0">
                <div class="custom-control custom-checkbox">
                    <input type="hidden" name="is_active" value="0">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="custom-control-input"
                        id="lotteryActive"
                        {{ old('is_active', $isEdit ? $lottery->is_active : true) ? 'checked' : '' }}
                    >
                    <label class="custom-control-label" for="lotteryActive">
                        Active lottery
                        <small class="d-block text-muted font-weight-normal">
                            Unchecked lotteries still sell tickets and draw, but winners are not announced or paid.
                        </small>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
