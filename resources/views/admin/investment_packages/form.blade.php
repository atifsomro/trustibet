@php
    $isEdit = isset($package);
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

    <div class="col-md-6 mb-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Features / description">{{ old('description', $package->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
