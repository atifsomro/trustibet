<section class="referral py-6">
    <div class="container">
        {{-- Stats --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-users text-4xl text-brand-primary"></i>
                    <p class="opacity-70 my-2">Total Referrals</p>
                    <h3 class="">25</h3>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-sack-dollar text-4xl text-green-500"></i>
                    <p class="opacity-70 my-2">Total Earnings</p>
                    <h3 class=" text-green-500">Rs.8,500</h3>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-hourglass-half text-4xl text-yellow-500"></i>
                    <p class="opacity-70 my-2">Pending Bonus</p>
                    <h3 class=" text-yellow-500">Rs.500</h3>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-user-check text-4xl text-blue-500"></i>
                    <p class="opacity-70 my-2">Active Referrals</p>
                    <h3 class="">12</h3>
                </div>
            </div>
        </div>
        {{-- Referral Banner --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-8">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Invite & Earn
                    </small>
                    <h3 class="mt-4">
                        Refer Friends & Earn Rewards
                    </h3>
                    <p class="mt-4 opacity-70">
                        Invite your friends to TrustiBet. Once they register, verify their account, and make their first
                        deposit, you'll automatically receive your referral bonus.
                    </p>
                </div>
                {{-- <div class="text-center">
                    <img src="{{ asset('images/referral/referral.png') }}" alt="Referral" class="max-w-sm mx-auto">
                </div> --}}
            </div>
        </div>
        {{-- Referral Code --}}
        <div class="grid xl:grid-cols-2 gap-6 mt-6">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">
                <h4>Your Referral Code</h4>
                <div class="mt-5 flex items-center justify-between rounded-2xl bg-brand-dark p-5">
                    <h3 id="referralCodeText" class="tracking-widest text-brand-primary">
                        TRUSTI4589
                    </h3>
                    <button id="copyReferralCode" class="btn-primary text-sm">
                        Copy
                    </button>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">
                <h4>Your Referral Link</h4>
                <div class="mt-5 flex items-center justify-between rounded-2xl bg-brand-dark p-5">
                    <span id="referralLinkText" class="truncate">
                        https://trustibet.com/register?ref=TRUSTI4589
                    </span>
                    <button id="copyReferralLink" class="btn-primary text-sm">
                        Copy
                    </button>
                </div>
            </div>
        </div>
        {{-- How It Works --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-8">
            <h3>How It Works</h3>
            <div class="grid md:grid-cols-4 gap-6 mt-8">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                        <i class="fa-solid fa-link text-brand-primary text-2xl"></i>
                    </div>
                    <h5 class="mt-5">
                        Share Link
                    </h5>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-brand-primary text-2xl"></i>
                    </div>
                    <h5 class="mt-5">
                        Friend Registers
                    </h5>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-brand-primary text-2xl"></i>
                    </div>
                    <h5 class="mt-5">
                        First Deposit
                    </h5>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-brand-primary/20 mx-auto flex items-center justify-center">
                        <i class="fa-solid fa-gift text-brand-primary text-2xl"></i>
                    </div>
                    <h5 class="mt-5">
                        Get Reward
                    </h5>
                </div>
            </div>
        </div>
        {{-- Referral History --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-8">
            <h3>Referral History</h3>
            <div class="overflow-x-auto mt-8">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-4 text-left">Friend</th>
                            <th class="py-4 text-left">Joined</th>
                            <th class="py-4 text-left">Deposit</th>
                            <th class="py-4 text-left">Reward</th>
                            <th class="py-4 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-brand-border">
                            <td class="py-5">Ali Khan</td>
                            <td class="py-5">12 Jul 2026</td>
                            <td class="py-5">Yes</td>
                            <td class="py-5 text-green-500">Rs.100</td>
                            <td class="py-5">
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                    Paid
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-brand-border">
                            <td class="py-5">Ahmed</td>
                            <td class="py-5">10 Jul 2026</td>
                            <td class="py-5">No</td>
                            <td class="py-5">--</td>
                            <td class="py-5">
                                <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-yellow-500">
                                    Pending
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-5">Bilal</td>
                            <td class="py-5">08 Jul 2026</td>
                            <td class="py-5">Yes</td>
                            <td class="py-5 text-green-500">Rs.100</td>
                            <td class="py-5">
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                    Paid
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Rules --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-8">
            <h3>Referral Rules</h3>
            <ul class="mt-2 space-y-2">
                <li>✅ Your friend must register using your referral link.</li>
                <li>✅ The account must complete KYC verification.</li>
                <li>✅ Your friend must make the first successful deposit.</li>
                <li>✅ Referral reward is credited automatically.</li>
                <li>✅ Self-referrals are not allowed.</li>
            </ul>
        </div>
    </div>
</section>
