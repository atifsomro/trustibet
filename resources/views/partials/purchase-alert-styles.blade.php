<style>
    .purchase-toast-stack,
    #lotteryToastStack {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 2147483646;
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: min(22rem, calc(100vw - 2rem));
        pointer-events: none;
    }

    .purchase-toast {
        pointer-events: auto;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid rgba(34, 197, 94, 0.35);
        background: rgba(15, 23, 42, 0.96);
        color: #4ade80;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        transform: translateX(24px);
        opacity: 0;
        transition: opacity 0.25s ease, transform 0.25s ease;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
        overflow: hidden;
    }

    .purchase-toast.is-visible {
        opacity: 1;
        transform: translateX(0);
    }

    .purchase-toast::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        background: linear-gradient(180deg, #4ade80, #fb923c);
    }

    .purchase-toast__icon {
        margin-top: 2px;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        border: 1px solid rgba(34, 197, 94, 0.25);
        background: rgba(34, 197, 94, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #4ade80;
    }

    .purchase-toast__body {
        min-width: 0;
        flex: 1;
        padding-right: 4px;
    }

    .purchase-toast__title {
        display: block;
        color: #4ade80;
        font-size: 14px;
        font-weight: 700;
    }

    .purchase-toast__message {
        display: block;
        margin-top: 4px;
        color: rgba(255, 255, 255, 0.7);
        font-size: 12px;
    }

    .purchase-toast__action {
        display: inline-flex;
        align-items: center;
        margin-top: 10px;
        padding: 6px 12px;
        border-radius: 10px;
        background: linear-gradient(90deg, #22c55e, #f97316);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 8px 18px rgba(34, 197, 94, 0.22);
    }

    .purchase-toast__action:hover {
        color: #fff;
    }

    .purchase-toast__close {
        border: 0;
        background: transparent;
        color: rgba(255, 255, 255, 0.55);
        cursor: pointer;
        padding: 2px 4px;
        line-height: 1;
        flex-shrink: 0;
    }

    .purchase-toast--warning {
        border-color: rgba(249, 115, 22, 0.4);
        color: #fb923c;
    }

    .purchase-toast--warning::before {
        background: linear-gradient(180deg, #fb923c, #f59e0b);
    }

    .purchase-toast--warning .purchase-toast__icon {
        border-color: rgba(249, 115, 22, 0.3);
        background: rgba(249, 115, 22, 0.12);
        color: #fb923c;
    }

    .purchase-toast--warning .purchase-toast__title {
        color: #fb923c;
    }

    .purchase-toast--error {
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
    }

    .purchase-toast--error::before {
        background: linear-gradient(180deg, #f87171, #f97316);
    }

    .purchase-toast--error .purchase-toast__icon {
        border-color: rgba(239, 68, 68, 0.3);
        background: rgba(239, 68, 68, 0.12);
        color: #f87171;
    }

    .purchase-toast--error .purchase-toast__title {
        color: #f87171;
    }

    .purchase-alert-host {
        display: none;
    }
</style>
