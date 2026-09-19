@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Settings</h4>
        </div>

        <p class="text-muted">
            Manage frontend configuration such as social links, contact details and other
            site-wide values. These are available anywhere in the app via
            <code>config('settings.your_key')</code>.
        </p>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            @forelse ($settingGroups as $group => $rows)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $group) }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($rows as $setting)
                            <div class="row align-items-start mb-3 pb-3 border-bottom setting-row">
                                <div class="col-md-3">
                                    <label class="form-label">Key</label>
                                    <input type="text" class="form-control" value="{{ $setting->key }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Label</label>
                                    <input type="text" name="settings[{{ $setting->id }}][label]"
                                        class="form-control" value="{{ old('settings.' . $setting->id . '.label', $setting->label) }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Type</label>
                                    <select name="settings[{{ $setting->id }}][type]" class="form-control">
                                        @foreach ($fieldTypes as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('settings.' . $setting->id . '.type', $setting->type) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Value</label>
                                    @if ($setting->type === 'textarea')
                                        <textarea name="settings[{{ $setting->id }}][value]" class="form-control" rows="2">{{ old('settings.' . $setting->id . '.value', $setting->value) }}</textarea>
                                    @elseif ($setting->type === 'boolean')
                                        <select name="settings[{{ $setting->id }}][value]" class="form-control">
                                            <option value="1" {{ old('settings.' . $setting->id . '.value', $setting->value) == '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ old('settings.' . $setting->id . '.value', $setting->value) == '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                    @else
                                        <input type="text" name="settings[{{ $setting->id }}][value]"
                                            class="form-control"
                                            value="{{ old('settings.' . $setting->id . '.value', $setting->value) }}">
                                    @endif
                                </div>
                                <div class="col-md-1 d-flex align-items-end h-100">
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm delete-setting"
                                        data-action="{{ route('admin.settings.destroy', $setting->id) }}"
                                        title="Remove field">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    No settings yet. Add your first field below — e.g. key <code>site_facebook</code>,
                    group <code>social</code>, type <code>URL</code>.
                </div>
            @endforelse

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Field</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-setting-field">
                        + Add Field
                    </button>
                </div>
                <div class="card-body">
                    <div id="new-settings-list"></div>
                    <p class="text-muted small mb-0" id="new-settings-empty">
                        Click "Add Field" to define a new configuration key.
                    </p>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Save Settings</button>
        </form>
    </div>

    <form id="delete-setting-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <template id="new-setting-template">
        <div class="row align-items-start mb-3 pb-3 border-bottom new-setting-row">
            <div class="col-md-2">
                <label class="form-label">Key</label>
                <input type="text" name="new[__INDEX__][key]" class="form-control" placeholder="site_facebook">
            </div>
            <div class="col-md-2">
                <label class="form-label">Label</label>
                <input type="text" name="new[__INDEX__][label]" class="form-control" placeholder="Facebook URL">
            </div>
            <div class="col-md-2">
                <label class="form-label">Group</label>
                <input type="text" name="new[__INDEX__][group]" class="form-control" placeholder="social" value="general">
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="new[__INDEX__][type]" class="form-control">
                    @foreach ($fieldTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Value</label>
                <input type="text" name="new[__INDEX__][value]" class="form-control" placeholder="https://facebook.com/yourpage">
            </div>
            <div class="col-md-1 d-flex align-items-end h-100">
                <button type="button" class="btn btn-outline-danger btn-sm remove-new-setting" title="Remove">
                    &times;
                </button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Add new dynamic fields ---
            const list = document.getElementById('new-settings-list');
            const addBtn = document.getElementById('add-setting-field');
            const template = document.getElementById('new-setting-template');
            const emptyHint = document.getElementById('new-settings-empty');
            let index = 0;

            if (addBtn && list && template) {
                addBtn.addEventListener('click', function () {
                    const clone = template.content.cloneNode(true);
                    clone.querySelectorAll('[name*="__INDEX__"]').forEach(function (el) {
                        el.name = el.name.replace('__INDEX__', index);
                    });
                    index++;
                    list.appendChild(clone);
                    if (emptyHint) {
                        emptyHint.style.display = 'none';
                    }
                });

                list.addEventListener('click', function (event) {
                    const removeBtn = event.target.closest('.remove-new-setting');
                    if (!removeBtn) {
                        return;
                    }
                    removeBtn.closest('.new-setting-row').remove();
                    if (emptyHint && list.children.length === 0) {
                        emptyHint.style.display = '';
                    }
                });
            }

            // --- Delete an existing field ---
            const deleteForm = document.getElementById('delete-setting-form');
            document.querySelectorAll('.delete-setting').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (!confirm('Remove this setting field? This cannot be undone.')) {
                        return;
                    }
                    deleteForm.action = btn.dataset.action;
                    deleteForm.submit();
                });
            });
        });
    </script>
@endsection
