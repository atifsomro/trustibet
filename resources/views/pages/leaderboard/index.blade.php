@extends('layouts.master')

@section('title', 'Leader Board | TrustiBet')

@section('content')
    <section class="py-8">
        <div class="container">

            {{-- Heading --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
                <div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-medium uppercase tracking-[3px] text-green-500">
                        <i class="fa-solid fa-trophy"></i>
                        TrustiBet
                    </span>
                    <h2 class="mt-3">Leader Board</h2>
                    <p class="mt-2 opacity-70">
                        Top 20 earners for {{ $board['date'] }}. Resets every day at midnight.
                    </p>
                </div>
                <div class="rounded-2xl border border-orange-500/20 bg-brand-surface px-5 py-3 text-sm">
                    <span class="opacity-70">Resets in</span>
                    <span id="lbCountdown" class="ml-2 font-bold text-orange-400" data-reset="{{ $board['resets_at'] }}">--:--:--</span>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div
                    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-[0_0_30px_rgba(34,197,94,.18)]">
                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                    <div class="p-6">
                        <p class="opacity-70">Today's Winners</p>
                        <span class="text-xl md:text-2xl lg:text-4xl font-extrabold text-green-500">{{ number_format($board['stats']['players']) }}</span>
                    </div>
                </div>
                <div
                    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-orange-500 hover:shadow-[0_0_30px_rgba(249,115,22,.18)]">
                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                    <div class="p-6">
                        <p class="opacity-70">Total Won Today</p>
                        <span class="text-xl md:text-2xl lg:text-4xl text-orange-400 font-extrabold">{{ $board['stats']['total'] }}</span>
                    </div>
                </div>
                <div
                    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-[0_0_30px_rgba(34,197,94,.18)]">
                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                    <div class="p-6">
                        <p class="opacity-70">Latest Win</p>
                        <span class="text-xl md:text-2xl lg:text-4xl text-green-500 font-extrabold">{{ $board['stats']['last_win'] ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Table card --}}
            <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="p-3 md:p-6 border-b border-brand-border">
                    <input id="lbSearch" type="text" placeholder="Search by player name..."
                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none focus:border-green-500">
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-[12px] md:text-base lg:text-xl">
                        <thead class="border-b border-brand-border bg-gradient-to-r from-green-500/10 to-orange-500/10">
                            <tr>
                                <th class="p-3 md:p-5 text-left text-green-500">Rank</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Player</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Today's Winnings</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Wins</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Last Win</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Action</th>
                            </tr>
                        </thead>
                        <tbody id="lbBody">
                            @forelse ($board['rows'] as $row)
                                <tr class="lb-row border-b border-brand-border last:border-b-0 hover:bg-brand-dark duration-300"
                                    data-name="{{ strtolower($row['name']) }}">
                                    <td class="p-3 md:p-5 font-bold">
                                        @if ($row['rank'] <= 3)
                                            <i class="fa-solid fa-trophy {{ ['', 'text-yellow-400', 'text-gray-300', 'text-orange-500'][$row['rank']] }} mr-1"></i>
                                        @endif
                                        #{{ $row['rank'] }}
                                    </td>
                                    <td class="p-3 md:p-5">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $row['avatar'] }}" alt="" loading="lazy"
                                                class="w-12 h-12 rounded-full object-cover border-2 border-green-500/50">
                                            <h6>{{ $row['name'] }}</h6>
                                        </div>
                                    </td>
                                    <td class="p-3 md:p-5 text-orange-400 font-semibold">{{ $row['total_formatted'] }}</td>
                                    <td class="p-3 md:p-5">{{ $row['hits'] }}</td>
                                    <td class="p-3 md:p-5">{{ $row['last_at'] }}</td>
                                    <td class="p-3 md:p-5">
                                        <button type="button" class="viewWinner btn-orange !w-auto px-4 text-sm"
                                            data-rank="{{ $row['rank'] }}">
                                            <i class="fa-solid fa-eye mr-2"></i>
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center opacity-70">
                                        No winners yet today. Be the first on the board!
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="lbNoMatch" class="hidden">
                                <td colspan="6" class="p-10 text-center opacity-70">No players match your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Winner modal popup --}}
    <div id="winnerModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 p-5">
        <div
            class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.18),0_0_70px_rgba(249,115,22,.10)]">
            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

            <div class="p-8 relative">
                <button id="closeWinnerModal" type="button"
                    class="absolute right-5 top-5 text-2xl opacity-70 hover:opacity-100 hover:text-orange-400 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div id="winnerLoading" class="py-16 text-center text-3xl text-green-500">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </div>
                <div id="winnerError" class="hidden py-16 text-center opacity-70">
                    Could not load player details. Please try again.
                </div>

                <div id="winnerContent" class="hidden">
                    <div class="text-center">
                        <img id="winnerAvatar" src="" alt=""
                            class="w-28 h-28 rounded-full object-cover mx-auto border-4 border-green-500 shadow-[0_0_25px_rgba(34,197,94,.3)]">
                        <h3 id="winnerName" class="mt-5"></h3>
                        <span id="winnerRank"
                            class="inline-block mt-3 px-4 py-2 rounded-full text-sm bg-green-500/20 text-green-500"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-5 mt-8">
                        <div class="rounded-2xl bg-brand-dark border border-orange-500/20 p-4">
                            <p class="opacity-60 text-sm">Today's Winnings</p>
                            <h5 id="winnerTotal" class="text-orange-400 mt-2"></h5>
                        </div>
                        <div class="rounded-2xl bg-brand-dark border border-green-500/20 p-4">
                            <p class="opacity-60 text-sm">Wins Today</p>
                            <h5 id="winnerHits" class="text-green-500 mt-2"></h5>
                        </div>
                        <div class="rounded-2xl bg-brand-dark border border-green-500/20 p-4">
                            <p class="opacity-60 text-sm">Country</p>
                            <h5 id="winnerCountry" class="mt-2"></h5>
                        </div>
                        <div class="rounded-2xl bg-brand-dark border border-orange-500/20 p-4">
                            <p class="opacity-60 text-sm">Member Since</p>
                            <h5 id="winnerSince" class="mt-2"></h5>
                        </div>
                    </div>

                    <div id="winnerBreakdown" class="mt-6 space-y-3"></div>

                    <div class="mt-8 text-center">
                        <p class="font-semibold bg-gradient-to-r from-green-500 to-orange-500 bg-clip-text text-transparent">
                            🎉 Congratulations to our lucky winner!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var detailUrl = @json(route('leaderboard.user', ['rank' => '__RANK__']));
            var modal = document.getElementById('winnerModal');
            var cache = {};
            var xhr = null;

            function show(el, on) { el.classList.toggle('hidden', !on); }
            function openModal() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
            function closeModal() {
                if (xhr) { xhr.abort(); }
                modal.classList.add('hidden'); modal.classList.remove('flex');
            }

            function render(d) {
                document.getElementById('winnerAvatar').src = d.avatar;
                document.getElementById('winnerName').textContent = d.name;
                document.getElementById('winnerRank').textContent = 'Rank #' + d.rank + ' Today';
                document.getElementById('winnerTotal').textContent = d.total;
                document.getElementById('winnerHits').textContent = d.transactions;
                document.getElementById('winnerCountry').textContent = d.country;
                document.getElementById('winnerSince').textContent = d.member_since || '—';

                var box = document.getElementById('winnerBreakdown');
                box.innerHTML = '';
                d.breakdown.forEach(function(b) {
                    var row = document.createElement('div');
                    row.className = 'flex items-center justify-between rounded-xl bg-brand-dark border border-brand-border px-4 py-3 text-sm';
                    var l = document.createElement('span');
                    l.textContent = b.label + ' (' + b.hits + ')';
                    var v = document.createElement('span');
                    v.className = 'font-semibold text-orange-400';
                    v.textContent = b.total;
                    row.appendChild(l); row.appendChild(v);
                    box.appendChild(row);
                });

                show(document.getElementById('winnerLoading'), false);
                show(document.getElementById('winnerError'), false);
                show(document.getElementById('winnerContent'), true);
            }

            $(document).on('click', '.viewWinner', function() {
                var rank = $(this).data('rank');
                show(document.getElementById('winnerContent'), false);
                show(document.getElementById('winnerError'), false);
                show(document.getElementById('winnerLoading'), true);
                openModal();

                if (cache[rank]) { return render(cache[rank]); }

                xhr = $.ajax({
                    url: detailUrl.replace('__RANK__', rank),
                    method: 'GET',
                    dataType: 'json',
                    headers: { 'Accept': 'application/json' },
                    success: function(d) { cache[rank] = d; render(d); },
                    error: function(_, status) {
                        if (status === 'abort') { return; }
                        show(document.getElementById('winnerLoading'), false);
                        show(document.getElementById('winnerError'), true);
                    }
                });
            });

            $('#closeWinnerModal').on('click', closeModal);
            $(modal).on('click', function(e) { if (e.target === modal) { closeModal(); } });
            $(document).on('keydown', function(e) { if (e.key === 'Escape') { closeModal(); } });

            // Client-side search (max 20 rows)
            $('#lbSearch').on('input', function() {
                var q = this.value.trim().toLowerCase(), any = false;
                $('.lb-row').each(function() {
                    var hit = !q || this.dataset.name.indexOf(q) !== -1;
                    this.classList.toggle('hidden', !hit);
                    any = any || hit;
                });
                show(document.getElementById('lbNoMatch'), !any && $('.lb-row').length > 0);
            });

            // Countdown to the daily reset; reload when the day rolls over.
            var cd = document.getElementById('lbCountdown');
            var resetAt = new Date(cd.dataset.reset).getTime();
            function tick() {
                var left = Math.max(0, Math.floor((resetAt - Date.now()) / 1000));
                if (left === 0) {
                    var k = 'lbReloaded', seen = null;
                    try { seen = sessionStorage.getItem(k); sessionStorage.setItem(k, String(resetAt)); } catch (e) {}
                    if (seen !== String(resetAt)) { location.reload(); }
                    cd.textContent = '00:00:00';
                    return;
                }
                var p = function(n) { return String(n).padStart(2, '0'); };
                cd.textContent = p(Math.floor(left / 3600)) + ':' + p(Math.floor(left % 3600 / 60)) + ':' + p(left % 60);
            }
            tick(); setInterval(tick, 1000);
        })();
    </script>
@endpush
