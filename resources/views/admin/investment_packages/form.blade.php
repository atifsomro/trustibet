@php
    $defaultFeaturePoints = \App\Models\InvestmentPackage::defaultFeaturePoints();
    $existingFeaturePoints = $package->feature_points ?? null;
    $featurePoints = old(
        'feature_points',
        (is_array($existingFeaturePoints) && count($existingFeaturePoints) > 0)
            ? $existingFeaturePoints
            : $defaultFeaturePoints
    );
    if (! is_array($featurePoints) || count($featurePoints) === 0) {
        $featurePoints = $defaultFeaturePoints;
    }
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Package Name</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $package->name ?? '') }}" placeholder="e.g. BASIC">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Package Price ($)</label>
        <input type="number" step="0.01" min="0.01" name="price"
            class="form-control @error('price') is-invalid @enderror"
            value="{{ old('price', $package->price ?? '') }}">
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Daily ROI ($)</label>
        <input type="number" step="0.01" min="0" name="daily_roi"
            class="form-control @error('daily_roi') is-invalid @enderror"
            value="{{ old('daily_roi', $package->daily_roi ?? '') }}">
        @error('daily_roi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Total Days</label>
        <input type="number" min="1" name="total_days"
            class="form-control @error('total_days') is-invalid @enderror"
            value="{{ old('total_days', $package->total_days ?? '') }}">
        @error('total_days')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" min="0" name="sort_order"
            class="form-control @error('sort_order') is-invalid @enderror"
            value="{{ old('sort_order', $package->sort_order ?? 0) }}">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label d-block">Recommended</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_recommended" value="1" class="form-check-input" id="is_recommended"
                {{ old('is_recommended', $package->is_recommended ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_recommended">Show Recommended badge</label>
        </div>
    </div>

    <div class="col-md-12 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Feature Points</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-feature-point">
                + Add Point
            </button>
        </div>
        <p class="text-muted small mb-2">
            These appear as checklist items on the investment card (e.g. “300% Deposit Bonus”).
        </p>

        <div id="feature-points-list">
            @foreach ($featurePoints as $index => $point)
                <div class="input-group mb-2 feature-point-row">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-check"></i></span>
                    </div>
                    <input type="text" name="feature_points[]"
                        class="form-control @error('feature_points.' . $index) is-invalid @enderror"
                        value="{{ $point }}"
                        placeholder="e.g. Highest Daily Earnings">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-danger remove-feature-point" title="Remove">
                            &times;
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @error('feature_points')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @error('feature_points.*')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<template id="feature-point-template">
    <div class="input-group mb-2 feature-point-row">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-check"></i></span>
        </div>
        <input type="text" name="feature_points[]" class="form-control"
            placeholder="e.g. Highest Daily Earnings">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-danger remove-feature-point" title="Remove">
                &times;
            </button>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('feature-points-list');
        const addBtn = document.getElementById('add-feature-point');
        const template = document.getElementById('feature-point-template');

        if (!list || !addBtn || !template) {
            return;
        }

        addBtn.addEventListener('click', function() {
            list.appendChild(template.content.cloneNode(true));
        });

        list.addEventListener('click', function(event) {
            const removeBtn = event.target.closest('.remove-feature-point');
            if (!removeBtn) {
                return;
            }

            const rows = list.querySelectorAll('.feature-point-row');
            const row = removeBtn.closest('.feature-point-row');

            if (rows.length <= 1) {
                const input = row.querySelector('input');
                if (input) {
                    input.value = '';
                }
                return;
            }

            row.remove();
        });
    });
</script>
