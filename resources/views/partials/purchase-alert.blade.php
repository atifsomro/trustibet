@php
    $type = $type ?? 'success';
    $title = $title ?? ($type === 'warning' ? 'Insufficient Balance' : ($type === 'error' ? 'Unable to Complete' : 'Payment Successful'));
    $message = $message ?? '';
    $actionLabel = $actionLabel ?? null;
    $actionUrl = $actionUrl ?? null;
    $icon = $type === 'warning' ? 'fa-triangle-exclamation' : ($type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check');
@endphp

<div class="purchase-alert purchase-alert--{{ $type }}" data-purchase-alert="{{ $type }}" role="status">
    <span class="purchase-alert__icon">
        <i class="fa-solid {{ $icon }} text-sm"></i>
    </span>
    <div class="purchase-alert__body">
        <strong class="purchase-alert__title">{{ $title }}</strong>
        <p class="purchase-alert__message">{{ $message }}</p>
        @if ($actionLabel && $actionUrl)
            <a href="{{ $actionUrl }}" class="purchase-alert__action">
                {{ $actionLabel }}
            </a>
        @endif
    </div>
    <button type="button" class="purchase-alert-dismiss purchase-alert__dismiss" aria-label="Dismiss">
        <i class="fa-solid fa-xmark text-sm"></i>
    </button>
</div>
