<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\GameType;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GamePlay;
use App\Models\User;
use App\Services\Game\LimitedDrawService;
use App\Models\GamePackage;
use App\Models\GamePrize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::query()->withCount(['packages', 'plays']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $games = $query->ordered()->paginate(20)->withQueryString();

        return view('admin.games.index', [
            'games' => $games,
            'types' => GameType::cases(),
            'active' => 'games',
        ]);
    }

    public function create()
    {
        return view('admin.games.create', [
            'types' => GameType::cases(),
            'existingTypes' => $this->existingTypeValues(),
            'active' => 'games',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateGame($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['config'] = $this->normalizeConfig(
            GameType::from($validated['type']),
            $validated['config'] ?? []
        );
        $validated = $this->applyUploads($request, $validated);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        try {
            $game = Game::create($validated);
            $this->syncLimitedDraw($game);

            return redirect()
                ->route('admin.games.edit', $game)
                ->with('success', 'Game created successfully.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Unable to create game.');
        }
    }

    public function edit(Game $game)
    {
        $game->load([
            'packages' => fn ($q) => $q->ordered()->with([
                'prizes' => fn ($pq) => $pq->ordered(),
            ]),
        ]);

        $limitedDraw = null;

        if ($game->type === GameType::LIMITED_DRAW) {
            $limitedDraw = $this->limitedDrawSnapshot($game);
        }

        return view('admin.games.edit', [
            'game' => $game,
            'types' => GameType::cases(),
            'limitedDraw' => $limitedDraw,
            'active' => 'games',
        ]);
    }

    public function participants(Game $game): JsonResponse
    {
        abort_unless($game->type === GameType::LIMITED_DRAW, 404);

        $snapshot = $this->limitedDrawSnapshot($game);
        $round = $snapshot['round'];
        $favoriteUserId = (int) ($snapshot['favorite_user']['id'] ?? 0);

        $plays = collect();

        if ($round) {
            $plays = GamePlay::query()
                ->where('game_id', $game->id)
                ->where('game_round_id', $round->id)
                ->with(['user:id,name,username,email,phone'])
                ->orderBy('id')
                ->get()
                ->map(function (GamePlay $play, int $index) use ($favoriteUserId) {
                    return [
                        'id' => $play->id,
                        'entry_number' => (int) data_get($play->selection, 'entry', $index + 1),
                        'user_id' => $play->user_id,
                        'name' => $play->user?->name ?: '—',
                        'username' => $play->user?->username ?: '—',
                        'email' => $play->user?->email ?: '—',
                        'phone' => $play->user?->phone ?: '—',
                        'fee' => (float) $play->fee_amount,
                        'status' => $play->status?->value,
                        'status_label' => $play->status?->label() ?? '—',
                        'is_favorite' => $favoriteUserId > 0 && (int) $play->user_id === $favoriteUserId,
                        'joined_at' => $play->created_at?->format('d M Y, h:i:s A'),
                        'joined_at_iso' => $play->created_at?->toIso8601String(),
                    ];
                })
                ->values();
        }

        return response()->json([
            'success' => true,
            'game_id' => $game->id,
            'round' => $snapshot['round_payload'],
            'live_participants' => $snapshot['entries'],
            'max_entries' => $snapshot['max_entries'],
            'is_open' => $snapshot['is_open'],
            'favorite_user' => $snapshot['favorite_user'],
            'participants' => $plays,
            'count' => $plays->count(),
            'server_now' => now()->toIso8601String(),
        ]);
    }

    public function updateFavoriteUser(Request $request, Game $game): JsonResponse
    {
        abort_unless($game->type === GameType::LIMITED_DRAW, 404);

        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $userId = isset($validated['user_id']) ? (int) $validated['user_id'] : 0;
        $snapshot = $this->limitedDrawSnapshot($game);
        $round = $snapshot['round'];

        if ($userId > 0) {
            if (! $round) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active round found for this draw.',
                ], 422);
            }

            $joined = GamePlay::query()
                ->where('game_id', $game->id)
                ->where('game_round_id', $round->id)
                ->where('user_id', $userId)
                ->exists();

            if (! $joined) {
                return response()->json([
                    'success' => false,
                    'message' => 'Favourite user must already be a participant in this round.',
                ], 422);
            }
        }

        $config = $game->config ?? [];

        if ($userId > 0) {
            $config['favorite_user_id'] = $userId;
        } else {
            unset($config['favorite_user_id']);
        }

        $game->update(['config' => $config]);

        $fresh = $this->limitedDrawSnapshot($game->fresh());

        return response()->json([
            'success' => true,
            'message' => $userId > 0
                ? 'Favourite winner selected.'
                : 'Favourite winner cleared. Draw will run normally.',
            'favorite_user' => $fresh['favorite_user'],
            'live_participants' => $fresh['entries'],
            'round' => $fresh['round_payload'],
            'is_open' => $fresh['is_open'],
        ]);
    }

    /**
     * Accurate live snapshot for the current (or latest) limited-draw round.
     *
     * @return array{
     *     round: ?\App\Models\GameRound,
     *     round_payload: ?array,
     *     entries: int,
     *     max_entries: int,
     *     is_open: bool,
     *     favorite_user: ?array
     * }
     */
    protected function limitedDrawSnapshot(Game $game): array
    {
        $service = app(LimitedDrawService::class);
        $round = $service->sync($game);
        $present = $service->present($game);

        // Prefer the synced round; present() re-syncs and may return the same row.
        $round = $present['round'] ?? $round;
        $entries = (int) ($present['entries'] ?? 0);

        // Double-check count directly from plays for the active round so admin stays exact.
        if ($round) {
            $entries = $round->plays()->count();
        }

        return [
            'round' => $round,
            'round_payload' => $round ? [
                'id' => $round->id,
                'round_number' => $round->round_number,
                'status' => $round->status?->value,
                'starts_at' => $round->starts_at?->toIso8601String(),
                'ends_at' => $round->ends_at?->toIso8601String(),
                'ends_at_label' => $round->ends_at?->format('d M Y, h:i A'),
            ] : null,
            'entries' => $entries,
            'max_entries' => (int) ($present['max_entries'] ?? 0),
            'is_open' => (bool) ($present['is_open'] ?? false),
            'favorite_user' => $this->favoriteUserPayload($game),
        ];
    }

    protected function favoriteUserPayload(Game $game): ?array
    {
        $userId = (int) $game->configValue('favorite_user_id', 0);

        if ($userId <= 0) {
            return null;
        }

        $user = User::query()
            ->select(['id', 'name', 'username', 'email'])
            ->find($userId);

        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name ?: '—',
            'username' => $user->username ?: '—',
            'email' => $user->email ?: '—',
            'label' => trim(($user->name ?: $user->username ?: 'User').' #'.$user->id),
        ];
    }

    public function update(Request $request, Game $game)
    {
        $validated = $this->validateGame($request, $game);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['config'] = $this->normalizeConfig(
            GameType::from($validated['type']),
            $validated['config'] ?? [],
            $game
        );
        $validated = $this->applyUploads($request, $validated, $game);

        try {
            $game->update($validated);
            $this->syncLimitedDraw($game->fresh());

            return redirect()
                ->route('admin.games.edit', $game)
                ->with('success', 'Game updated successfully.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Unable to update game.');
        }
    }

    public function storePackage(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'fee' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
            'meta.chances' => ['nullable', 'integer', 'min:1'],
            'meta.spins' => ['nullable', 'integer', 'min:1'],
            'meta.multiplier' => ['nullable', 'numeric', 'min:0'],
            'meta.chip_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $game->packages()->create([
            'name' => $validated['name'],
            'fee' => $validated['fee'],
            'meta' => $this->cleanMeta($validated['meta'] ?? []),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Package added.');
    }

    public function updatePackage(Request $request, Game $game, GamePackage $package)
    {
        abort_unless($package->game_id === $game->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'fee' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
            'meta.chances' => ['nullable', 'integer', 'min:1'],
            'meta.spins' => ['nullable', 'integer', 'min:1'],
            'meta.multiplier' => ['nullable', 'numeric', 'min:0'],
            'meta.chip_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $package->update([
            'name' => $validated['name'],
            'fee' => $validated['fee'],
            'meta' => $this->cleanMeta($validated['meta'] ?? []),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Package updated.');
    }

    public function destroyPackage(Game $game, GamePackage $package)
    {
        abort_unless($package->game_id === $game->id, 404);
        $package->delete();

        return back()->with('success', 'Package deleted.');
    }

    public function storePrize(Request $request, Game $game, GamePackage $package)
    {
        abort_unless($package->game_id === $game->id, 404);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'prize_amount' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
            'meta.color' => ['nullable', 'string', 'max:40'],
            'meta.segment' => ['nullable', 'integer', 'min:0'],
            'meta.free_spins' => ['nullable', 'integer', 'min:0'],
        ]);

        $package->prizes()->create([
            'label' => $validated['label'],
            'prize_amount' => $validated['prize_amount'],
            'weight' => $validated['weight'],
            'meta' => $this->prizeMeta($game, $validated['meta'] ?? []),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Prize added.');
    }

    public function updatePrize(Request $request, Game $game, GamePackage $package, GamePrize $prize)
    {
        abort_unless($package->game_id === $game->id, 404);
        abort_unless($prize->game_package_id === $package->id, 404);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'prize_amount' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
            'meta.color' => ['nullable', 'string', 'max:40'],
            'meta.segment' => ['nullable', 'integer', 'min:0'],
            'meta.free_spins' => ['nullable', 'integer', 'min:0'],
        ]);

        $prize->update([
            'label' => $validated['label'],
            'prize_amount' => $validated['prize_amount'],
            'weight' => $validated['weight'],
            'meta' => $this->prizeMeta($game, $validated['meta'] ?? []),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return back()->with('success', 'Prize updated.');
    }

    public function destroyPrize(Game $game, GamePackage $package, GamePrize $prize)
    {
        abort_unless($package->game_id === $game->id, 404);
        abort_unless($prize->game_package_id === $package->id, 404);
        $prize->delete();

        return back()->with('success', 'Prize deleted.');
    }

    protected function validateGame(Request $request, ?Game $game = null): array
    {
        $typeRules = ['required', Rule::in(GameType::values())];

        if ($game === null) {
            $typeRules[] = Rule::unique('games', 'type');
        }

        return $request->validate([
            'type' => $typeRules,
            'slug' => [
                'nullable',
                'string',
                'max:120',
                'alpha_dash',
                Rule::unique('games', 'slug')->ignore($game?->id),
            ],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:32768'],
            'prize_image' => ['nullable', 'image', 'max:32768'],
            'badge' => ['nullable', 'string', 'max:40'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'config' => ['nullable', 'array'],
            'config.faces' => ['nullable', 'integer', 'min:2', 'max:20'],
            'config.free_spins_daily' => ['nullable', 'integer', 'min:0'],
            'config.round_seconds' => ['nullable', 'integer', 'min:5', 'max:300'],
            'config.lock_seconds' => ['nullable', 'integer', 'min:1', 'max:120'],
            'config.history_limit' => ['nullable', 'integer', 'min:1', 'max:5'],
            'config.max_bet_per_round' => ['nullable', 'numeric', 'min:0'],
            'config.max_bet_per_day' => ['nullable', 'numeric', 'min:0'],
            'config.colors' => ['nullable', 'string'],
            'config.headline' => ['nullable', 'string', 'max:160'],
            'config.prize_name' => ['nullable', 'string', 'max:160'],
            'config.prize_value' => ['nullable', 'numeric', 'min:0'],
            'config.prize_image' => ['nullable', 'string', 'max:255'],
            'config.currency' => ['nullable', 'string', 'max:12'],
            'config.draw_at' => ['nullable', 'date'],
            'config.max_entries' => ['nullable', 'integer', 'min:0'],
            'config.winner_count' => ['nullable', 'integer', 'min:1'],
            'config.max_per_user' => ['nullable', 'integer', 'min:1'],
        ], [
            'type.unique' => 'This game already exists.',
        ]);
    }

    protected function existingTypeValues(): array
    {
        return Game::query()
            ->pluck('type')
            ->map(fn ($type) => $type instanceof GameType ? $type->value : (string) $type)
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeConfig(GameType $type, array $config, ?Game $existing = null): array
    {
        $normalized = [];

        if ($type === GameType::DICE) {
            $normalized['faces'] = (int) ($config['faces'] ?? 6);
        }

        if ($type === GameType::WHEEL) {
            $normalized['free_spins_daily'] = (int) ($config['free_spins_daily'] ?? 0);
        }

        if ($type === GameType::COLOR_TRADING) {
            $normalized['round_seconds'] = (int) ($config['round_seconds'] ?? 10);
            $normalized['lock_seconds'] = (int) ($config['lock_seconds'] ?? 5);
            $normalized['history_limit'] = max(1, min(5, (int) ($config['history_limit'] ?? 3)));
            $normalized['max_bet_per_round'] = max(0, (float) ($config['max_bet_per_round'] ?? 2000));
            $normalized['max_bet_per_day'] = max(0, (float) ($config['max_bet_per_day'] ?? 10000));
            $colors = $config['colors'] ?? '';

            if (is_string($colors)) {
                $normalized['colors'] = collect(explode(',', $colors))
                    ->map(fn ($c) => strtolower(trim($c)))
                    ->filter()
                    ->values()
                    ->all();
            } elseif (is_array($colors)) {
                $normalized['colors'] = array_values(array_filter(array_map(
                    fn ($c) => strtolower(trim((string) $c)),
                    $colors
                )));
            }
        }

        if ($type === GameType::LIMITED_DRAW) {
            $normalized['headline'] = trim((string) ($config['headline'] ?? ''));
            $normalized['prize_name'] = trim((string) ($config['prize_name'] ?? ''));
            $normalized['prize_value'] = (float) ($config['prize_value'] ?? 0);
            $normalized['prize_image'] = trim((string) ($config['prize_image'] ?? ''));
            $normalized['currency'] = trim((string) ($config['currency'] ?? '$')) ?: '$';
            $normalized['draw_at'] = ! empty($config['draw_at'])
                ? \Illuminate\Support\Carbon::parse($config['draw_at'])->toDateTimeString()
                : null;
            $normalized['max_entries'] = (int) ($config['max_entries'] ?? 0);
            $normalized['winner_count'] = max(1, (int) ($config['winner_count'] ?? 1));
            $normalized['max_per_user'] = max(1, (int) ($config['max_per_user'] ?? 1));

            $favoriteUserId = (int) ($config['favorite_user_id']
                ?? $existing?->configValue('favorite_user_id', 0)
                ?? 0);

            if ($favoriteUserId > 0) {
                $normalized['favorite_user_id'] = $favoriteUserId;
            }
        }

        return $normalized;
    }

    protected function applyUploads(Request $request, array $validated, ?Game $game = null): array
    {
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('games', 'public');
        } elseif ($game) {
            $validated['image_path'] = $game->image_path;
        }

        $type = GameType::from($validated['type']);

        if ($type === GameType::LIMITED_DRAW) {
            if ($request->hasFile('prize_image')) {
                $validated['config']['prize_image'] = $request->file('prize_image')->store('games', 'public');
            } elseif ($game) {
                $validated['config']['prize_image'] = (string) $game->configValue('prize_image', '');
            }
        }

        return $validated;
    }

    protected function syncLimitedDraw(Game $game): void
    {
        if ($game->type !== GameType::LIMITED_DRAW) {
            return;
        }

        app(LimitedDrawService::class)->sync($game);
    }

    protected function prizeMeta(Game $game, array $meta): array
    {
        $meta = $this->cleanMeta($meta);

        if (in_array($game->type, [GameType::SCRATCH_CARD, GameType::DICE], true)) {
            unset($meta['color'], $meta['segment']);
        }

        return $meta;
    }

    protected function cleanMeta(array $meta): array
    {
        return collect($meta)
            ->reject(fn ($value) => $value === null || $value === '')
            ->map(function ($value) {
                if (is_numeric($value) && ! is_string($value)) {
                    return $value + 0;
                }

                return is_string($value) ? trim($value) : $value;
            })
            ->all();
    }
}
