<div id="purchase-alert-host" class="purchase-alert-host" aria-live="polite">
    @if (session('success'))
        @include('partials.purchase-alert', [
            'type' => 'success',
            'title' => 'Success',
            'message' => session('success'),
        ])
    @endif

    @if (session('error'))
        @include('partials.purchase-alert', [
            'type' => 'error',
            'title' => 'Unable to Complete',
            'message' => session('error'),
        ])
    @endif

    @if (session('warning'))
        @include('partials.purchase-alert', [
            'type' => 'warning',
            'title' => str_contains(strtolower((string) session('warning')), 'insufficient')
                ? 'Insufficient Balance'
                : 'Notice',
            'message' => session('warning'),
            'actionLabel' => str_contains(strtolower((string) session('warning')), 'insufficient')
                ? 'Deposit Now'
                : null,
            'actionUrl' => str_contains(strtolower((string) session('warning')), 'insufficient')
                ? route('deposits.index')
                : null,
        ])
    @endif

    @if ($errors->any())
        @include('partials.purchase-alert', [
            'type' => 'error',
            'title' => 'Unable to Complete',
            'message' => collect($errors->all())->implode(' '),
        ])
    @endif
</div>
