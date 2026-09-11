<div id="lotteryLiveNotices">
    @if (request()->boolean('new_round'))
        <div
            id="newRoundBanner"
            class="mx-auto mt-8 max-w-3xl rounded-2xl border border-green-500/20 bg-green-500/10 px-5 py-4 text-sm text-green-400"
        >
            <strong class="block">New round started.</strong>
            <span class="mt-1 block opacity-80">
                The previous draw is complete. Tickets are now on sale for the new round.
            </span>
        </div>
    @endif
</div>
