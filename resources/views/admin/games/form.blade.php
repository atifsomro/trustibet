@php
    $config = old('config', $game->config ?? []);
    $selectedType = old('type', (isset($game) && $game->exists) ? ($game->type?->value ?? '') : '');
    $colorsValue = old(
        'config.colors',
        is_array($config['colors'] ?? null) ? implode(', ', $config['colors']) : ($config['colors'] ?? '')
    );
    $isExisting = isset($game) && $game->exists;
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Game Type</label>
        <select id="game-type" name="type" class="form-control @error('type') is-invalid @enderror"
            {{ $isExisting ? 'disabled' : '' }} required>
            @unless ($isExisting)
                <option value="" @selected($selectedType === '')>Select game type</option>
            @endunless
            @foreach ($types as $type)
                <option value="{{ $type->value }}" @selected($selectedType === $type->value)>
                    {{ $type->dropdownLabel() }}
                </option>
            @endforeach
        </select>
        @if ($isExisting)
            <input type="hidden" name="type" value="{{ $game->type->value }}">
            <small class="text-muted">Game type cannot be changed after the game is created.</small>
        @else
            <small class="text-muted">Choose the game type first. Only that game’s settings will be shown.</small>
        @endif
        @error('type')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @unless ($isExisting)
            <div id="game-type-exists" class="alert alert-danger mt-3 mb-0" style="display: none;" role="alert">
                This game already exists.
            </div>
        @endunless
    </div>
</div>

<div id="game-details" class="row" @if ($selectedType === '') style="display: none !important" @endif>
    <div class="col-12 game-config" data-game-type="scratch_card" @if ($selectedType !== 'scratch_card') style="display: none !important" @endif>
        <hr>
        <h5>Configuration</h5>
        <p class="text-muted mb-0">Scratch cards use packages and prizes. There is no extra configuration for this type.</p>
    </div>

    <div class="col-12 game-config" data-game-type="dice" @if ($selectedType !== 'dice') style="display: none !important" @endif>
        <hr>
        <h5>Configuration</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Dice Faces</label>
                <input type="number" min="2" max="20" name="config[faces]" class="form-control"
                    value="{{ old('config.faces', $config['faces'] ?? 6) }}"
                    @disabled($selectedType !== 'dice')>
            </div>
        </div>
    </div>

    <div class="col-12 game-config" data-game-type="wheel" @if ($selectedType !== 'wheel') style="display: none !important" @endif>
        <hr>
        <h5>Configuration</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Free Spins / Day</label>
                <input type="number" min="0" name="config[free_spins_daily]" class="form-control"
                    value="{{ old('config.free_spins_daily', $config['free_spins_daily'] ?? 0) }}"
                    @disabled($selectedType !== 'wheel')>
            </div>
        </div>
    </div>

    <div class="col-12 game-config" data-game-type="color_trading" @if ($selectedType !== 'color_trading') style="display: none !important" @endif>
        <hr>
        <h5>Configuration</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Round Seconds</label>
                <input type="number" min="5" name="config[round_seconds]" class="form-control"
                    value="{{ old('config.round_seconds', $config['round_seconds'] ?? 10) }}"
                    @disabled($selectedType !== 'color_trading')>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Lock Seconds</label>
                <input type="number" min="1" name="config[lock_seconds]" class="form-control"
                    value="{{ old('config.lock_seconds', $config['lock_seconds'] ?? 5) }}"
                    @disabled($selectedType !== 'color_trading')>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Colors (comma separated)</label>
                <input type="text" name="config[colors]" class="form-control"
                    value="{{ $colorsValue }}"
                    placeholder="green, red, blue, yellow, orange, purple, pink, cyan, white, black"
                    @disabled($selectedType !== 'color_trading')>
            </div>
        </div>
    </div>

    <div class="col-12 game-config" data-game-type="limited_draw" @if ($selectedType !== 'limited_draw') style="display: none !important" @endif>
        <hr>
        <h5>Configuration</h5>
        <p class="text-muted">Entry fee is the package fee. A prize amount above 0 is paid to the wallet when the winner is drawn. Leave the prize amount at 0 for a physical prize.</p>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Home Headline</label>
                <input type="text" name="config[headline]" class="form-control"
                    value="{{ old('config.headline', $config['headline'] ?? '') }}"
                    placeholder="Win Amazing Rewards"
                    @disabled($selectedType !== 'limited_draw')>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Prize Name</label>
                <input type="text" name="config[prize_name]" class="form-control"
                    value="{{ old('config.prize_name', $config['prize_name'] ?? '') }}"
                    placeholder="Honda CG125"
                    @disabled($selectedType !== 'limited_draw')>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Prize Value</label>
                <input type="number" step="0.01" min="0" name="config[prize_value]" class="form-control"
                    value="{{ old('config.prize_value', $config['prize_value'] ?? 0) }}"
                    @disabled($selectedType !== 'limited_draw')>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Currency Label</label>
                <input type="text" name="config[currency]" class="form-control"
                    value="{{ old('config.currency', $config['currency'] ?? '$') }}"
                    placeholder="$"
                    @disabled($selectedType !== 'limited_draw')>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Prize photo</label>
                <input type="file" name="prize_image" accept="image/*"
                    class="form-control @error('prize_image') is-invalid @enderror"
                    @disabled($selectedType !== 'limited_draw')>
                <small class="text-muted">Choose a picture from your computer. This is the prize photo on the participate page. Leave it empty to keep the current picture.</small>
                @error('prize_image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @if (!empty($config['prize_image']))
                    <div class="mt-2">
                        <img src="{{ app(\App\Services\Game\LimitedDrawService::class)->publicUrl($config['prize_image']) }}"
                            alt="Current prize photo" width="140" class="border rounded">
                    </div>
                @endif
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Draw At</label>
                <input type="datetime-local" name="config[draw_at]" class="form-control"
                    value="{{ old('config.draw_at', !empty($config['draw_at']) ? \Illuminate\Support\Carbon::parse($config['draw_at'])->format('Y-m-d\TH:i') : '') }}"
                    @disabled($selectedType !== 'limited_draw')>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Max Entries</label>
                <input type="number" min="0" name="config[max_entries]" class="form-control"
                    value="{{ old('config.max_entries', $config['max_entries'] ?? 0) }}"
                    @disabled($selectedType !== 'limited_draw')>
                <small class="text-muted">0 = unlimited</small>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Winners</label>
                <input type="number" min="1" name="config[winner_count]" class="form-control"
                    value="{{ old('config.winner_count', $config['winner_count'] ?? 1) }}"
                    @disabled($selectedType !== 'limited_draw')>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Max / User</label>
                <input type="number" min="1" name="config[max_per_user]" class="form-control"
                    value="{{ old('config.max_per_user', $config['max_per_user'] ?? 1) }}"
                    @disabled($selectedType !== 'limited_draw')>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $game->title ?? '') }}">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
            value="{{ old('slug', $game->slug ?? '') }}" placeholder="scratch-card">
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Badge</label>
        <input type="text" name="badge" class="form-control" value="{{ old('badge', $game->badge ?? '') }}"
            placeholder="Popular">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $game->description ?? '') }}</textarea>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Card image</label>
        <input type="file" name="image" accept="image/*"
            class="form-control @error('image') is-invalid @enderror">
        <small class="text-muted">Choose a picture from your computer. This is the image on the home page and the game card. Leave it empty to keep the current picture.</small>
        @error('image')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @if (!empty($game->image_path))
            <div class="mt-2">
                <img src="{{ $game->imageUrl() }}" alt="Current card image" width="140" class="border rounded">
            </div>
        @endif
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Rating</label>
        <input type="number" step="0.1" min="0" max="5" name="rating" class="form-control"
            value="{{ old('rating', $game->rating ?? 4.5) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" min="0" name="sort_order" class="form-control"
            value="{{ old('sort_order', $game->sort_order ?? 0) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', $game->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label d-block">Featured</label>
        <div class="form-check mt-2">
            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured"
                {{ old('is_featured', $game->is_featured ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">Show on home</label>
        </div>
    </div>

</div>

@push('page_js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('game-type');
            const details = document.getElementById('game-details');
            const existsAlert = document.getElementById('game-type-exists');
            const takenTypes = @json($existingTypes ?? []);

            if (!typeSelect || !details) {
                return;
            }

            const showBlock = function (element, visible) {
                if (visible) {
                    element.style.removeProperty('display');
                } else {
                    element.style.setProperty('display', 'none', 'important');
                }
            };

            const applyType = function () {
                const type = typeSelect.value;
                const taken = type !== '' && takenTypes.indexOf(type) !== -1;
                const submitButton = typeSelect.form
                    ? typeSelect.form.querySelector('[type="submit"]')
                    : null;

                if (existsAlert) {
                    existsAlert.style.display = taken ? '' : 'none';
                }

                if (submitButton) {
                    submitButton.disabled = taken;
                }

                showBlock(details, type !== '' && !taken);

                details.querySelectorAll('.game-config').forEach(function (section) {
                    const active = !taken && section.dataset.gameType === type;
                    showBlock(section, active);
                    section.querySelectorAll('input, select, textarea').forEach(function (field) {
                        field.disabled = !active;
                    });
                });
            };

            typeSelect.addEventListener('change', applyType);
            applyType();
        });
    </script>
@endpush
