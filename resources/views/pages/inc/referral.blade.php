<div class="referral">
    {{-- Stats --}}
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
            <div class="flex flex-col text-center items-center justify-between">
                <i class="fa-solid fa-users text-xl sm:text-4xl text-brand-primary"></i>
                <p class="opacity-70 my-2">Total Referrals</p>
                <h3>{{ $referralStats['total'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
            <div class="flex flex-col text-center items-center justify-between">
                <i class="fa-solid fa-sack-dollar text-xl sm:text-4xl text-green-500"></i>
                <p class="opacity-70 my-2">Total Earnings</p>
                <h3 class="text-green-500">${{ number_format((float) ($referralStats['total_earnings'] ?? 0), 2) }}</h3>
            </div>
        </div>
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
            <div class="flex flex-col text-center items-center justify-between">
                <i class="fa-solid fa-hourglass-half text-xl sm:text-4xl text-yellow-500"></i>
                <p class="opacity-70 my-2">Pending Bonus</p>
                <h3 class="text-yellow-500">${{ number_format((float) ($referralStats['pending_bonus'] ?? 0), 2) }}</h3>
            </div>
        </div>
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-2 sm:p-6">
            <div class="flex flex-col text-center items-center justify-between">
                <i class="fa-solid fa-user-check text-xl sm:text-4xl text-blue-500"></i>
                <p class="opacity-70 my-2">Active Referrals</p>
                <h3>{{ $referralStats['active'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    {{-- Referral Banner --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="grid lg:grid-cols-2 gap-8 items-center">
            <div>
                <small class="uppercase tracking-[3px] text-brand-primary">
                    Invite & Earn
                </small>
                <h3 class="mt-4">
                    Refer Friends & Earn Rewards
                </h3>
                <p class="mt-4 opacity-70">
                    Invite your friends to TrustiBet. Once they register with your link and make their first
                    deposit, you earn {{ number_format((float) ($bonusPercent ?? 5), 0) }}% as a referral bonus.
                    Bonus unlocks after you make at least one deposit, and can only be used to invest or buy lottery tickets (not withdrawable).
                </p>
            </div>
        </div>
    </div>
    {{-- Referral Code --}}
    <div class="grid xl:grid-cols-2 gap-6 mt-6">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
            <h4>Your Referral Code</h4>
            <div class="mt-5 flex items-center justify-between rounded-2xl bg-brand-dark p-2 sm:p-5">
                <h3 id="referralCodeText" class="tracking-widest text-brand-primary">
                    {{ auth()->user()->referral_code }}
                </h3>
                <button type="button" id="copyReferralCode" class="btn-primary text-[10px] sm:text-sm">
                    Copy
                </button>
            </div>
        </div>
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
            <h4>Your Referral Link</h4>
            <div class="mt-5 flex items-center justify-between rounded-2xl bg-brand-dark p-3 sm:p-5 gap-2">
                <p id="referralLinkText" class="truncate text-[10px] sm:text-sm">
                    {{ $referralLink ?? '' }}
                </p>
                <button type="button" id="copyReferralLink" class="btn-primary text-[10px] sm:text-sm shrink-0">
                    Copy
                </button>
            </div>
        </div>
    </div>
    {{-- How It Works --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <h3>How It Works</h3>
        <div class="grid md:grid-cols-4 gap-6 mt-8">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                    <i class="fa-solid fa-link text-brand-primary text-xl sm:text-2xl"></i>
                </div>
                <h5 class="mt-5">
                    Share Link
                </h5>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                    <i class="fa-solid fa-user-plus text-brand-primary text-xl sm:text-2xl"></i>
                </div>
                <h5 class="mt-5">
                    Friend Registers
                </h5>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                    <i class="fa-solid fa-wallet text-brand-primary text-xl sm:text-2xl"></i>
                </div>
                <h5 class="mt-5">
                    First Deposit
                </h5>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                    <i class="fa-solid fa-gift text-brand-primary text-xl sm:text-2xl"></i>
                </div>
                <h5 class="mt-5">
                    Get Reward
                </h5>
            </div>
        </div>
    </div>
    {{-- Referral History --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <h3>Referral History</h3>
        <div class="overflow-x-auto mt-4 sm:mt-8">
            <table class="w-full min-w-[950px] text-[12px] md:text-base lg:text-xl">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="py-2 sm:py-4 text-left">Friend</th>
                        <th class="py-2 sm:py-4 text-left">Joined</th>
                        <th class="py-2 sm:py-4 text-left">Deposit</th>
                        <th class="py-2 sm:py-4 text-left">Reward</th>
                        <th class="py-2 sm:py-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($referrals ?? []) as $row)
                        @php
                            $invitee = $row->invitee;
                            $earning = $row->earning;
                        @endphp
                        <tr class="border-b border-brand-border">
                            <td class="py-3 sm:py-5">
                                {{ $invitee->username ?? '—' }}
                            </td>
                            <td class="py-3 sm:py-5">
                                {{ optional($invitee->created_at)->format('d M Y') ?? '—' }}
                            </td>
                            <td class="py-3 sm:py-5">
                                @if ($earning)
                                    ${{ number_format((float) $earning->deposit_amount, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 sm:py-5 {{ $earning?->isUnlocked() ? 'text-green-500' : ($earning?->isPending() ? 'text-yellow-500' : '') }}">
                                @if ($earning)
                                    ${{ number_format((float) $earning->bonus_amount, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 sm:py-5">
                                @if ($earning?->isUnlocked())
                                    <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                        Unlocked
                                    </span>
                                @elseif ($earning?->isPending())
                                    <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-yellow-500">
                                        Pending
                                    </span>
                                @else
                                    <span class="rounded-full bg-brand-border/40 px-3 py-1 opacity-70">
                                        Awaiting deposit
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center opacity-70">
                                No referrals yet. Share your link to start earning.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- Rules --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <h3>Referral Rules</h3>
        <ul class="mt-2 space-y-2">
            <li>Your friend must register using your referral link.</li>
            <li>Your friend must make their first successful deposit.</li>
            <li>You earn {{ number_format((float) ($bonusPercent ?? 5), 0) }}% of that first deposit as bonus.</li>
            <li>Bonus stays pending until you make at least one deposit yourself.</li>
            <li>Referral bonus is not withdrawable — only for investments and lottery.</li>
            <li>Self-referrals are not allowed.</li>
        </ul>
    </div>
</div>
