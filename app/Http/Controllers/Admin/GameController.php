<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\GameType;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Services\Game\LimitedDrawService;
use App\Models\GamePackage;
use App\Models\GamePrize;
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

        return view('admin.games.edit', [
            'game' => $game,
            'types' => GameType::cases(),
            'active' => 'games',
        ]);
    }

    public function update(Request $request, Game $game)
    {
        $validated = $this->validateGame($request, $game);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['config'] = $this->normalizeConfig(
            GameType::from($validated['type']),
            $validated['config'] ?? []
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

    public function destroy(Game $game)
    {
        try {
            $game->delete();

            return redirect()
                ->route('admin.games.index')
                ->with('success', 'Game deleted successfully.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Unable to delete game.');
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
        ]);

        $package->prizes()->create([
            'label' => $validated['label'],
            'prize_amount' => $validated['prize_amount'],
            'weight' => $validated['weight'],
            'meta' => $this->cleanMeta($validated['meta'] ?? []),
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
        ]);

        $prize->update([
            'label' => $validated['label'],
            'prize_amount' => $validated['prize_amount'],
            'weight' => $validated['weight'],
            'meta' => $this->cleanMeta($validated['meta'] ?? []),
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
        return $request->validate([
            'type' => ['required', Rule::in(GameType::values())],
            'slug' => [
                'nullable',
                'string',
                'max:120',
                'alpha_dash',
                Rule::unique('games', 'slug')->ignore($game?->id),
            ],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'prize_image' => ['nullable', 'image', 'max:4096'],
            'badge' => ['nullable', 'string', 'max:40'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'config' => ['nullable', 'array'],
            'config.faces' => ['nullable', 'integer', 'min:2', 'max:20'],
            'config.free_spins_daily' => ['nullable', 'integer', 'min:0'],
            'config.round_seconds' => ['nullable', 'integer', 'min:5', 'max:300'],
            'config.lock_seconds' => ['nullable', 'integer', 'min:1', 'max:120'],
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
        ]);
    }

    protected function normalizeConfig(GameType $type, array $config): array
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
            $normalized['currency'] = trim((string) ($config['currency'] ?? 'Rs.')) ?: 'Rs.';
            $normalized['draw_at'] = ! empty($config['draw_at'])
                ? \Illuminate\Support\Carbon::parse($config['draw_at'])->toDateTimeString()
                : null;
            $normalized['max_entries'] = (int) ($config['max_entries'] ?? 0);
            $normalized['winner_count'] = max(1, (int) ($config['winner_count'] ?? 1));
            $normalized['max_per_user'] = max(1, (int) ($config['max_per_user'] ?? 1));
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
