@extends('admin.base')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Edit Game — {{ $game->title }}</h4>
                <a href="{{ route('admin.games.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.games.update', $game) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.games.form')
                    <button type="submit" class="btn btn-success">Update Game</button>
                </form>
            </div>
        </div>

        @if ($game->type === \App\Enums\GameType::LIMITED_DRAW)
            @php
                $ld = $limitedDraw ?? null;
                $ldRound = $ld['round_payload'] ?? null;
                $ldEntries = (int) ($ld['entries'] ?? 0);
                $ldMax = (int) ($ld['max_entries'] ?? 0);
                $ldOpen = (bool) ($ld['is_open'] ?? false);
                $ldFavorite = $ld['favorite_user'] ?? null;
            @endphp
            <div class="card mb-4" id="limited-draw-live"
                data-participants-url="{{ route('admin.games.participants', $game) }}"
                data-favorite-url="{{ route('admin.games.favorite-user', $game) }}"
                data-csrf="{{ csrf_token() }}">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Live Round Participants</h5>
                    <span class="badge badge-{{ $ldOpen ? 'success' : 'secondary' }}" id="ld-status-badge">
                        {{ $ldOpen ? 'Live' : 'Closed' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small text-uppercase">Live Participants</div>
                                <div class="h3 mb-0 mt-1" id="ld-live-count">{{ number_format($ldEntries) }}</div>
                                <small class="text-muted" id="ld-max-label">
                                    @if ($ldMax > 0)
                                        / {{ number_format($ldMax) }} max
                                    @else
                                        unlimited
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="text-muted small">Current Round</div>
                            <div class="font-weight-bold" id="ld-round-label">
                                @if ($ldRound)
                                    Round #{{ $ldRound['round_number'] }}
                                    <span class="text-muted font-weight-normal">
                                        ({{ $ldRound['status'] ?? '—' }})
                                    </span>
                                @else
                                    No round yet
                                @endif
                            </div>
                            <div class="text-muted small mt-1" id="ld-ends-label">
                                @if (!empty($ldRound['ends_at_label']))
                                    Draw at {{ $ldRound['ends_at_label'] }}
                                @else
                                    Draw time not set
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <button type="button" class="btn btn-primary" id="ld-view-participants"
                                @disabled(empty($ldRound))>
                                <i class="fa fa-users"></i> View Participants
                            </button>
                            <div class="small text-muted mt-2" id="ld-last-updated">
                                Updates every 5 seconds
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="text-muted small text-uppercase">Favourite Winner</div>
                            <div class="font-weight-bold" id="ld-favorite-label">
                                @if ($ldFavorite)
                                    {{ $ldFavorite['label'] }}
                                    <span class="text-muted font-weight-normal">
                                        ({{ $ldFavorite['email'] }})
                                    </span>
                                @else
                                    None — draw will pick randomly
                                @endif
                            </div>
                            <small class="text-muted">
                                Open participants and click “Set Favourite”. If unset, the draw is random.
                                Favourite is cleared automatically after the draw.
                            </small>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="ld-clear-favorite"
                            @disabled(empty($ldFavorite))>
                            Clear Favourite
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="ldParticipantsModal" tabindex="-1" role="dialog"
                aria-labelledby="ldParticipantsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ldParticipantsModalLabel">
                                Round Participants
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <strong id="ld-modal-round">—</strong>
                                    <span class="text-muted ml-2" id="ld-modal-count">0 participants</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="ld-refresh-list">
                                    Refresh
                                </button>
                            </div>
                            <div id="ld-favorite-alert" class="alert alert-warning py-2" style="{{ empty($ldFavorite) ? 'display:none' : '' }}">
                                Favourite:
                                <strong id="ld-modal-favorite">
                                    {{ $ldFavorite['label'] ?? '—' }}
                                </strong>
                            </div>
                            <div class="table-responsive" style="max-height: 60vh; overflow: auto;">
                                <table class="table table-sm table-striped table-bordered mb-0">
                                    <thead class="thead-light" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>#</th>
                                            <th>Entry</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Fee</th>
                                            <th>Status</th>
                                            <th>Joined At</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="ld-participants-body">
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">Loading…</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Packages (fees)</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.games.packages.store', $game) }}" class="row g-2 mb-4">
                    @csrf
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Package name" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" min="0" name="fee" class="form-control"
                            placeholder="Fee" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" min="0" name="meta[multiplier]" class="form-control"
                            placeholder="Multiplier">
                    </div>
                    <div class="col-md-2">
                        <input type="number" min="1" name="meta[chances]" class="form-control"
                            placeholder="Chances">
                    </div>
                    <div class="col-md-1">
                        <input type="number" min="0" name="sort_order" class="form-control" value="0"
                            placeholder="Sort">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Add Package</button>
                    </div>
                </form>

                @forelse ($game->packages as $package)
                    <div class="border rounded p-3 mb-4">
                        <form method="POST"
                            action="{{ route('admin.games.packages.update', [$game, $package]) }}"
                            class="row g-2 align-items-end">
                            @csrf
                            @method('PUT')
                            <div class="col-md-3">
                                <label class="small">Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ $package->name }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="small">Fee</label>
                                <input type="number" step="0.01" min="0" name="fee" class="form-control"
                                    value="{{ $package->fee }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="small">Multiplier</label>
                                <input type="number" step="0.01" min="0" name="meta[multiplier]"
                                    class="form-control" value="{{ $package->metaValue('multiplier') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="small">Chances</label>
                                <input type="number" min="1" name="meta[chances]" class="form-control"
                                    value="{{ $package->metaValue('chances') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="small">Sort</label>
                                <input type="number" min="0" name="sort_order" class="form-control"
                                    value="{{ $package->sort_order }}">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                        id="pkg_active_{{ $package->id }}"
                                        {{ $package->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pkg_active_{{ $package->id }}">Active</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-info text-white">Save</button>
                            </div>
                        </form>
                        <form method="POST"
                            action="{{ route('admin.games.packages.destroy', [$game, $package]) }}"
                            class="d-inline"
                            onsubmit="return confirm('Delete this package and its prizes?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger mt-2">Delete Package</button>
                        </form>

                        <hr>
                        <h6>Prizes / Odds for {{ $package->name }}</h6>
                        @php
                            $showColorAndSegment = ! in_array($game->type, [
                                \App\Enums\GameType::SCRATCH_CARD,
                                \App\Enums\GameType::DICE,
                            ], true);
                        @endphp
                        <form method="POST"
                            action="{{ route('admin.games.packages.prizes.store', [$game, $package]) }}"
                            class="row g-2 mb-3">
                            @csrf
                            <div class="{{ $showColorAndSegment ? 'col-md-3' : 'col-md-4' }}">
                                <input type="text" name="label" class="form-control" placeholder="Label"
                                    required>
                            </div>
                            <div class="{{ $showColorAndSegment ? 'col-md-2' : 'col-md-3' }}">
                                <input type="number" step="0.01" min="0" name="prize_amount"
                                    class="form-control" placeholder="Amount" required>
                            </div>
                            <div class="{{ $showColorAndSegment ? 'col-md-2' : 'col-md-3' }}">
                                <input type="number" min="0" name="weight" class="form-control"
                                    placeholder="Weight" value="1" required>
                            </div>
                            @if ($showColorAndSegment)
                                <div class="col-md-2">
                                    <input type="text" name="meta[color]" class="form-control"
                                        placeholder="Color (optional)">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" min="0" name="meta[segment]" class="form-control"
                                        placeholder="Segment #">
                                </div>
                            @endif
                            <div class="{{ $showColorAndSegment ? 'col-md-1' : 'col-md-2' }}">
                                <button class="btn btn-primary w-100">Add</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Amount</th>
                                        <th>Weight</th>
                                        @if ($showColorAndSegment)
                                            <th>Color</th>
                                            <th>Segment</th>
                                        @endif
                                        <th>Active</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($package->prizes as $prize)
                                        <tr>
                                            <form method="POST"
                                                action="{{ route('admin.games.packages.prizes.update', [$game, $package, $prize]) }}">
                                                @csrf
                                                @method('PUT')
                                                <td>
                                                    <input type="text" name="label" class="form-control form-control-sm"
                                                        value="{{ $prize->label }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="prize_amount"
                                                        class="form-control form-control-sm"
                                                        value="{{ $prize->prize_amount }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" min="0" name="weight"
                                                        class="form-control form-control-sm"
                                                        value="{{ $prize->weight }}" required>
                                                    <input type="hidden" name="sort_order"
                                                        value="{{ $prize->sort_order }}">
                                                </td>
                                                @if ($showColorAndSegment)
                                                    <td>
                                                        <input type="text" name="meta[color]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $prize->metaValue('color') }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" name="meta[segment]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $prize->metaValue('segment') }}">
                                                    </td>
                                                @endif
                                                <td>
                                                    <input type="checkbox" name="is_active" value="1"
                                                        {{ $prize->is_active ? 'checked' : '' }}>
                                                </td>
                                                <td class="text-nowrap">
                                                    <button class="btn btn-xs btn-info text-white btn-sm">Save</button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('admin.games.packages.prizes.destroy', [$game, $package, $prize]) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Delete prize?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-xs btn-danger btn-sm">Del</button>
                                            </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $showColorAndSegment ? 7 : 5 }}" class="text-center text-muted">No prizes yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No packages yet. Add one above.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@if ($game->type === \App\Enums\GameType::LIMITED_DRAW)
@push('page_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const root = document.getElementById('limited-draw-live');
        if (!root) {
            return;
        }

        const url = root.dataset.participantsUrl;
        const favoriteUrl = root.dataset.favoriteUrl;
        const csrf = root.dataset.csrf;
        const liveCountEl = document.getElementById('ld-live-count');
        const maxLabelEl = document.getElementById('ld-max-label');
        const roundLabelEl = document.getElementById('ld-round-label');
        const endsLabelEl = document.getElementById('ld-ends-label');
        const statusBadgeEl = document.getElementById('ld-status-badge');
        const lastUpdatedEl = document.getElementById('ld-last-updated');
        const favoriteLabelEl = document.getElementById('ld-favorite-label');
        const favoriteAlertEl = document.getElementById('ld-favorite-alert');
        const modalFavoriteEl = document.getElementById('ld-modal-favorite');
        const clearFavoriteBtn = document.getElementById('ld-clear-favorite');
        const viewBtn = document.getElementById('ld-view-participants');
        const refreshBtn = document.getElementById('ld-refresh-list');
        const modalRoundEl = document.getElementById('ld-modal-round');
        const modalCountEl = document.getElementById('ld-modal-count');
        const bodyEl = document.getElementById('ld-participants-body');

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatNumber(value) {
            return Number(value || 0).toLocaleString();
        }

        function applyFavorite(favorite) {
            if (favoriteLabelEl) {
                if (favorite) {
                    favoriteLabelEl.innerHTML = escapeHtml(favorite.label)
                        + ' <span class="text-muted font-weight-normal">('
                        + escapeHtml(favorite.email)
                        + ')</span>';
                } else {
                    favoriteLabelEl.textContent = 'None — draw will pick randomly';
                }
            }

            if (clearFavoriteBtn) {
                clearFavoriteBtn.disabled = !favorite;
            }

            if (favoriteAlertEl && modalFavoriteEl) {
                if (favorite) {
                    favoriteAlertEl.style.display = '';
                    modalFavoriteEl.textContent = favorite.label;
                } else {
                    favoriteAlertEl.style.display = 'none';
                    modalFavoriteEl.textContent = '—';
                }
            }
        }

        function applySummary(data) {
            const round = data.round;

            if (liveCountEl) {
                liveCountEl.textContent = formatNumber(data.live_participants);
            }

            if (maxLabelEl) {
                maxLabelEl.textContent = data.max_entries > 0
                    ? '/ ' + formatNumber(data.max_entries) + ' max'
                    : 'unlimited';
            }

            if (roundLabelEl) {
                if (round) {
                    roundLabelEl.innerHTML = 'Round #' + escapeHtml(round.round_number)
                        + ' <span class="text-muted font-weight-normal">('
                        + escapeHtml(round.status || '—')
                        + ')</span>';
                } else {
                    roundLabelEl.textContent = 'No round yet';
                }
            }

            if (endsLabelEl) {
                endsLabelEl.textContent = round && round.ends_at_label
                    ? 'Draw at ' + round.ends_at_label
                    : 'Draw time not set';
            }

            if (statusBadgeEl) {
                statusBadgeEl.textContent = data.is_open ? 'Live' : 'Closed';
                statusBadgeEl.className = 'badge badge-' + (data.is_open ? 'success' : 'secondary');
            }

            if (viewBtn) {
                viewBtn.disabled = !round;
            }

            applyFavorite(data.favorite_user || null);

            if (lastUpdatedEl) {
                const now = new Date();
                lastUpdatedEl.textContent = 'Updated '
                    + now.toLocaleTimeString()
                    + ' · refreshes every 5s';
            }
        }

        function renderList(data) {
            const round = data.round;
            const rows = data.participants || [];

            if (modalRoundEl) {
                modalRoundEl.textContent = round
                    ? 'Round #' + round.round_number + ' (' + (round.status || '—') + ')'
                    : 'No round';
            }

            if (modalCountEl) {
                modalCountEl.textContent = formatNumber(data.count) + ' participant'
                    + (data.count === 1 ? '' : 's');
            }

            applyFavorite(data.favorite_user || null);

            if (!bodyEl) {
                return;
            }

            if (!rows.length) {
                bodyEl.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No participants in this round yet.</td></tr>';
                return;
            }

            bodyEl.innerHTML = rows.map(function (row, index) {
                const favoriteBtn = row.is_favorite
                    ? '<span class="badge badge-warning">Favourite</span>'
                    : '<button type="button" class="btn btn-xs btn-outline-warning ld-set-favorite" data-user-id="'
                        + escapeHtml(row.user_id)
                        + '">Set Favourite</button>';

                return '<tr class="' + (row.is_favorite ? 'table-warning' : '') + '">'
                    + '<td>' + (index + 1) + '</td>'
                    + '<td>' + escapeHtml(row.entry_number) + '</td>'
                    + '<td>' + escapeHtml(row.name) + '</td>'
                    + '<td>' + escapeHtml(row.username) + '</td>'
                    + '<td>' + escapeHtml(row.email) + '</td>'
                    + '<td>' + escapeHtml(row.phone) + '</td>'
                    + '<td>' + formatNumber(row.fee) + '</td>'
                    + '<td>' + escapeHtml(row.status_label) + '</td>'
                    + '<td>' + escapeHtml(row.joined_at) + '</td>'
                    + '<td class="text-nowrap">' + favoriteBtn + '</td>'
                    + '</tr>';
            }).join('');
        }

        async function fetchParticipants(includeList) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to load participants');
            }

            const data = await response.json();
            applySummary(data);

            if (includeList) {
                renderList(data);
            }

            return data;
        }

        async function setFavorite(userId) {
            const response = await fetch(favoriteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    user_id: userId || null,
                }),
            });

            const data = await response.json();

            if (!response.ok || data.success === false) {
                throw new Error(data.message || 'Unable to update favourite.');
            }

            applyFavorite(data.favorite_user || null);
            await fetchParticipants(true);

            return data;
        }

        async function openModal() {
            if (bodyEl) {
                bodyEl.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">Loading…</td></tr>';
            }

            try {
                await fetchParticipants(true);
            } catch (error) {
                if (bodyEl) {
                    bodyEl.innerHTML = '<tr><td colspan="10" class="text-center text-danger py-4">Unable to load participants.</td></tr>';
                }
            }

            if (window.jQuery) {
                window.jQuery('#ldParticipantsModal').modal('show');
            }
        }

        viewBtn?.addEventListener('click', openModal);
        refreshBtn?.addEventListener('click', async function () {
            refreshBtn.disabled = true;
            try {
                await fetchParticipants(true);
            } catch (error) {
                if (bodyEl) {
                    bodyEl.innerHTML = '<tr><td colspan="10" class="text-center text-danger py-4">Unable to refresh list.</td></tr>';
                }
            } finally {
                refreshBtn.disabled = false;
            }
        });

        clearFavoriteBtn?.addEventListener('click', async function () {
            if (!confirm('Clear favourite winner? The draw will pick randomly.')) {
                return;
            }

            clearFavoriteBtn.disabled = true;
            try {
                await setFavorite(null);
            } catch (error) {
                alert(error.message || 'Unable to clear favourite.');
            } finally {
                clearFavoriteBtn.disabled = false;
            }
        });

        bodyEl?.addEventListener('click', async function (event) {
            const button = event.target.closest('.ld-set-favorite');
            if (!button) {
                return;
            }

            const userId = Number(button.dataset.userId || 0);
            if (!userId) {
                return;
            }

            button.disabled = true;
            try {
                await setFavorite(userId);
            } catch (error) {
                alert(error.message || 'Unable to set favourite.');
                button.disabled = false;
            }
        });

        // Live polling. Refresh the modal table too when it is open.
        setInterval(function () {
            const modalOpen = window.jQuery
                ? window.jQuery('#ldParticipantsModal').hasClass('show')
                : false;
            fetchParticipants(modalOpen).catch(function () {});
        }, 5000);
    });
</script>
@endpush
@endif
