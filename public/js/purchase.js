(function () {
    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function money(amount, currency) {
        const formatted = Number(amount || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        return (currency || 'USD') + ' ' + formatted;
    }

    function ensureToastStack() {
        let stack = document.getElementById('lotteryToastStack') || document.getElementById('purchaseToastStack');

        if (stack) {
            return stack;
        }

        stack = document.createElement('div');
        stack.id = 'purchaseToastStack';
        stack.className = 'purchase-toast-stack';
        stack.setAttribute('aria-live', 'polite');
        document.body.appendChild(stack);

        return stack;
    }

    function alertIcon(type) {
        if (type === 'warning') {
            return 'fa-triangle-exclamation';
        }

        if (type === 'error') {
            return 'fa-circle-exclamation';
        }

        return 'fa-circle-check';
    }

    function showPurchaseAlert(options) {
        const type = options.type || 'success';
        const stack = ensureToastStack();
        const toast = document.createElement('div');
        const action = options.action || null;

        toast.className = 'purchase-toast purchase-toast--' + type;
        toast.setAttribute('role', 'status');

        const actionHtml = action && action.label && action.url
            ? '<a href="' + escapeHtml(action.url) + '" class="purchase-toast__action">' + escapeHtml(action.label) + '</a>'
            : '';

        toast.innerHTML =
            '<div class="purchase-toast__icon"><i class="fa-solid ' + alertIcon(type) + '"></i></div>' +
            '<div class="purchase-toast__body">' +
                '<strong class="purchase-toast__title">' + escapeHtml(options.title || 'Payment Successful') + '</strong>' +
                '<span class="purchase-toast__message">' + escapeHtml(options.message || '') + '</span>' +
                actionHtml +
            '</div>' +
            '<button type="button" class="purchase-toast__close" aria-label="Dismiss" data-purchase-toast-close="1">' +
                '<i class="fa-solid fa-xmark"></i>' +
            '</button>';

        stack.appendChild(toast);

        requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });

        const dismiss = function () {
            toast.classList.remove('is-visible');
            setTimeout(function () {
                toast.remove();
            }, 250);
        };

        toast.querySelector('[data-purchase-toast-close]')?.addEventListener('click', dismiss);
        setTimeout(dismiss, action ? 6000 : 3000);
    }

    function parseErrorPayload(data, fallback) {
        if (!data || typeof data !== 'object') {
            return { type: 'error', title: 'Unable to Complete', message: fallback };
        }

        if (data.message && data.errors) {
            const firstError = Object.values(data.errors).flat()[0];
            return {
                type: data.type || 'error',
                title: data.title || 'Unable to Complete',
                message: firstError || data.message || fallback,
                action: data.action || null,
            };
        }

        return {
            type: data.type || 'error',
            title: data.title || 'Unable to Complete',
            message: data.message || fallback,
            action: data.action || null,
        };
    }

    function updateLotteryWallet(wallet) {
        if (!wallet) {
            return;
        }

        const currency = wallet.walletCurrency || 'USD';
        const used = document.querySelector('[data-lottery-balance="used"]');
        const remaining = document.querySelector('[data-lottery-balance="remaining"]');

        if (used) {
            used.textContent = money(wallet.usedBalance, currency);
        }

        if (remaining) {
            remaining.textContent = money(wallet.remainingBalance, currency);
        }
    }

    function updateLotteryCard(state) {
        if (!state || !state.id) {
            return;
        }

        const remainingLabels = document.querySelectorAll('[data-lottery-remaining-label="' + state.id + '"]');
        const remainingCounts = document.querySelectorAll('[data-lottery-remaining-count="' + state.id + '"]');
        const userTickets = document.querySelectorAll('[data-lottery-user-tickets="' + state.id + '"]');
        const sold = document.querySelectorAll('[data-lottery-sold="' + state.id + '"]');
        const slots = document.querySelectorAll('[data-lottery-buy-slot="' + state.id + '"]');

        remainingLabels.forEach(function (el) {
            el.innerHTML = '<i class="fa-solid fa-ticket mr-1"></i> ' + escapeHtml(state.remaining_label);
        });

        remainingCounts.forEach(function (el) {
            el.textContent = state.remaining_tickets === null
                ? 'Unlimited'
                : Number(state.remaining_tickets).toLocaleString();
        });

        userTickets.forEach(function (el) {
            el.textContent = state.user_tickets_label;
        });

        sold.forEach(function (el) {
            el.textContent = Number(state.tickets_sold || 0).toLocaleString();
        });

        if (!state.can_buy) {
            const replacement = state.sales_open
                ? '<div class="mt-2 rounded-2xl border border-orange-500/20 bg-orange-500/10 py-4 text-center text-sm font-semibold text-orange-400">Ticket limit reached</div>'
                : '<div class="mt-2 block w-full rounded-2xl border border-gray-500/20 bg-gray-500/10 py-2 text-center text-sm font-semibold opacity-60">Sales Closed</div>';

            slots.forEach(function (slot) {
                slot.innerHTML = replacement;
            });
        }
    }

    function updateInvestmentStats(payload) {
        const wallet = (payload && payload.wallet) || {};
        const stats = (payload && payload.stats) || {};

        const withdrawable = document.querySelector('[data-invest-stat="withdrawable"]');
        const totalInvested = document.querySelector('[data-invest-stat="total-invested"]');
        const activePlan = document.querySelector('[data-invest-stat="active-plan"]');
        const daysLeft = document.querySelector('[data-invest-stat="days-left"]');

        if (withdrawable && typeof wallet.withdrawable === 'number') {
            withdrawable.textContent = '$' + Number(wallet.withdrawable).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        if (totalInvested && typeof stats.total_invested === 'number') {
            totalInvested.textContent = '$' + Number(stats.total_invested).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        if (activePlan && stats.active_plan) {
            activePlan.textContent = stats.active_plan;
        }

        if (daysLeft && stats.days_left !== undefined && stats.days_left !== null) {
            daysLeft.textContent = stats.days_left + ' days';
        }
    }

    async function submitPurchaseForm(form) {
        const confirmMessage = form.getAttribute('data-confirm');
        if (confirmMessage && !window.confirm(confirmMessage)) {
            return;
        }

        const button = form.querySelector('button[type="submit"]');
        const originalHtml = button ? button.innerHTML : '';
        if (button) {
            button.disabled = true;
            button.classList.add('opacity-70', 'pointer-events-none');
            button.textContent = 'Processing...';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
                body: new FormData(form),
            });

            const data = await response.json().catch(function () {
                return null;
            });

            if (!data) {
                showPurchaseAlert({
                    type: 'error',
                    title: 'Unable to Complete',
                    message: 'Unable to complete the purchase.',
                });
                return;
            }

            if (!response.ok || data.ok === false) {
                showPurchaseAlert(parseErrorPayload(data, 'Unable to complete the purchase.'));
                return;
            }

            showPurchaseAlert({
                type: (data && data.type) || 'success',
                title: (data && data.title) || 'Payment Successful',
                message: (data && data.message) || 'Payment completed successfully.',
                action: (data && data.action) || null,
            });

            if (data && data.lottery) {
                updateLotteryCard(data.lottery);
            }

            if (data && data.wallet && (data.wallet.usedBalance !== undefined || data.wallet.remainingBalance !== undefined)) {
                updateLotteryWallet(data.wallet);
            }

            if (form.matches('[data-purchase-type="investment"]')) {
                updateInvestmentStats(data || {});
            }
        } catch (error) {
            showPurchaseAlert({
                type: 'error',
                title: 'Unable to Complete',
                message: 'Something went wrong. Please try again.',
            });
        } finally {
            if (button && form.isConnected) {
                button.disabled = false;
                button.classList.remove('opacity-70', 'pointer-events-none');
                button.innerHTML = originalHtml;
            }
        }
    }

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form.js-ajax-purchase');
        if (!form) {
            return;
        }

        event.preventDefault();
        submitPurchaseForm(form);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const host = document.getElementById('purchase-alert-host');
        if (!host) {
            return;
        }

        host.querySelectorAll('[data-purchase-alert]').forEach(function (alert) {
            const actionLink = alert.querySelector('a.purchase-alert__action, a.purchase-toast__action');
            showPurchaseAlert({
                type: alert.getAttribute('data-purchase-alert') || 'success',
                title: (alert.querySelector('.purchase-alert__title, .purchase-toast__title') || {}).textContent || 'Success',
                message: (alert.querySelector('.purchase-alert__message, .purchase-toast__message') || {}).textContent || '',
                action: actionLink ? { label: actionLink.textContent.trim(), url: actionLink.getAttribute('href') } : null,
            });
        });

        host.innerHTML = '';
    });
})();
