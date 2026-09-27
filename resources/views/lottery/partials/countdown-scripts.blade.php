<script>
document.addEventListener('DOMContentLoaded', function () {
    const recentNewRoundToasts = new Map();

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

    function formatRemaining(differenceMs) {
        const totalSeconds = Math.max(0, Math.ceil(differenceMs / 1000));
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        return String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');
    }

    function ensureToastStack() {
        let stack = document.getElementById('lotteryToastStack');

        if (!stack) {
            stack = document.createElement('div');
            stack.id = 'lotteryToastStack';
            stack.setAttribute('aria-live', 'polite');
            stack.style.cssText = [
                'position:fixed',
                'top:16px',
                'right:16px',
                'z-index:2147483646',
                'display:flex',
                'flex-direction:column',
                'gap:10px',
                'width:min(22rem,calc(100vw - 2rem))',
                'pointer-events:none',
            ].join(';');
            document.body.appendChild(stack);
        }

        return stack;
    }

    function showNewRoundToast(lotteryTitle, message, lotteryId) {
        const key = String(lotteryId || lotteryTitle || 'lottery');
        const now = Date.now();
        const lastShown = recentNewRoundToasts.get(key) || 0;

        // Avoid duplicate toasts from draw retries within a few seconds.
        if (now - lastShown < 4000) {
            return;
        }

        recentNewRoundToasts.set(key, now);

        const stack = ensureToastStack();
        const toast = document.createElement('div');
        const title = String(lotteryTitle || '').trim();

        toast.setAttribute('role', 'status');
        toast.style.cssText = [
            'pointer-events:auto',
            'display:flex',
            'align-items:flex-start',
            'gap:12px',
            'padding:14px 16px',
            'border-radius:14px',
            'border:1px solid rgba(34,197,94,.35)',
            'background:rgba(15,23,42,.96)',
            'color:#4ade80',
            'box-shadow:0 12px 30px rgba(0,0,0,.35)',
            'transform:translateX(24px)',
            'opacity:0',
            'transition:opacity .25s ease, transform .25s ease',
            'font-size:14px',
            'line-height:1.4',
        ].join(';');

        toast.innerHTML =
            '<div style="margin-top:2px;width:32px;height:32px;border-radius:10px;border:1px solid rgba(34,197,94,.25);background:rgba(34,197,94,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                '<i class="fa-solid fa-rotate" style="color:#4ade80;"></i>' +
            '</div>' +
            '<div style="min-width:0;flex:1;">' +
                '<strong style="display:block;color:#4ade80;font-size:14px;">New round started</strong>' +
                (title
                    ? '<span style="display:block;margin-top:2px;color:#fff;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escapeHtml(title) + '</span>'
                    : '') +
                '<span style="display:block;margin-top:4px;color:rgba(255,255,255,.7);font-size:12px;">' +
                    escapeHtml(message || 'Tickets are now on sale for the new round.') +
                '</span>' +
            '</div>' +
            '<button type="button" aria-label="Dismiss" data-lottery-toast-close="1" style="border:0;background:transparent;color:rgba(255,255,255,.55);cursor:pointer;padding:2px 4px;line-height:1;">' +
                '<i class="fa-solid fa-xmark"></i>' +
            '</button>';

        stack.appendChild(toast);

        requestAnimationFrame(function () {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        });

        const dismiss = function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(24px)';
            setTimeout(function () {
                toast.remove();
            }, 250);
        };

        toast.querySelector('[data-lottery-toast-close]')?.addEventListener('click', dismiss);
        setTimeout(dismiss, 3000);
    }

    window.showNewRoundToast = showNewRoundToast;

    function showNewRoundBanner(message) {
        const notices = document.getElementById('lotteryLiveNotices');
        showNewRoundToast(notices?.dataset?.lotteryTitle || '', message);
    }

    const params = new URLSearchParams(window.location.search);

    if (params.get('new_round') === '1') {
        params.delete('new_round');
        const query = params.toString();
        window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
        const notices = document.getElementById('lotteryLiveNotices');
        showNewRoundToast(notices?.dataset?.lotteryTitle || '');
    }

    function showPersonalResultNotice(personal) {
        if (!personal || !personal.outcome || personal.outcome === 'guest' || personal.outcome === 'not_entered') {
            return;
        }

        let notice = document.getElementById('personalResultNotice');
        const host = document.getElementById('lotteryLiveNotices') || document.querySelector('.container');

        if (!notice && host) {
            notice = document.createElement('div');
            notice.id = 'personalResultNotice';
            notice.className = 'mx-auto mt-4 max-w-3xl rounded-2xl border px-5 py-4 text-sm';
            host.prepend(notice);
        }

        if (!notice) {
            return;
        }

        if (personal.outcome === 'won' && Array.isArray(personal.wins) && personal.wins.length) {
            const win = personal.wins[0];
            notice.className = 'mx-auto mt-4 max-w-3xl rounded-2xl border border-green-500/20 bg-green-500/10 px-5 py-4 text-sm text-green-400';
            notice.innerHTML =
                '<strong class="block">You won ' + (win.label || 'a prize') + '!</strong>' +
                '<span class="mt-1 block opacity-80">' +
                (win.currency || '') + ' ' + Number(win.amount || 0).toFixed(2) +
                (win.ticket_number ? ' · Ticket ' + win.ticket_number : '') +
                '. This result is private.</span>';
        } else if (personal.outcome === 'lost') {
            notice.className = 'mx-auto mt-4 max-w-3xl rounded-2xl border border-orange-500/20 bg-orange-500/10 px-5 py-4 text-sm text-orange-400';
            notice.innerHTML =
                '<strong class="block">No win this round.</strong>' +
                '<span class="mt-1 block opacity-80">Your result is private and only visible to you.</span>';
        }

        notice.hidden = false;
    }

    function updateWallet(wallet) {
        if (!wallet) {
            return;
        }

        const currency = wallet.currency || 'USD';
        const used = document.querySelector('[data-lottery-balance="used"]');
        const remaining = document.querySelector('[data-lottery-balance="remaining"]');

        if (used) {
            used.textContent = currency + ' ' + Number(wallet.used || 0).toFixed(2);
        }

        if (remaining) {
            remaining.textContent = currency + ' ' + Number(wallet.remaining || 0).toFixed(2);
        }

        if (typeof syncWalletBalance === "function" && typeof wallet.remaining !== "undefined") {
            syncWalletBalance(wallet.remaining);
        }
    }

    function stopCountdown(element) {
        if (element && typeof element._lotteryCountdownStop === 'function') {
            element._lotteryCountdownStop();
        }
    }

    function refreshLotteryShell(lotteryId, liveHtmlUrl) {
        if (!liveHtmlUrl) {
            return Promise.resolve();
        }

        return fetch(liveHtmlUrl, {
            headers: {
                'Accept': 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                const current = document.querySelector('[data-lottery-shell="' + lotteryId + '"]');
                if (!current) {
                    return;
                }

                current.querySelectorAll('.lottery-countdown').forEach(stopCountdown);

                const template = document.createElement('div');
                template.innerHTML = html.trim();
                const next = template.firstElementChild;

                if (!next) {
                    return;
                }

                current.replaceWith(next);
                next.querySelectorAll('.lottery-countdown').forEach(startCountdown);
            })
            .catch(function () {
                // Keep the countdown running even if the card refresh fails.
            });
    }

    function startCountdown(element) {
        if (element.dataset.countdownBound === '1') {
            return;
        }

        element.dataset.countdownBound = '1';

        let endDate = new Date(element.dataset.end).getTime();
        let clockOffsetMs = 0;
        let intervalId = null;
        let retryTimer = null;
        let drawing = false;
        let stopped = false;
        let drawAttempt = 0;

        function stop() {
            stopped = true;
            drawing = false;

            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }

            if (retryTimer) {
                clearTimeout(retryTimer);
                retryTimer = null;
            }
        }

        element._lotteryCountdownStop = stop;

        function syncClock(serverNowIso) {
            if (!serverNowIso) {
                return;
            }

            const serverNow = new Date(serverNowIso).getTime();

            if (!Number.isNaN(serverNow)) {
                clockOffsetMs = serverNow - Date.now();
            }
        }

        syncClock(element.dataset.serverNow);

        function nowMs() {
            return Date.now() + clockOffsetMs;
        }

        function remainingMs() {
            return endDate - nowMs();
        }

        function ensureInterval() {
            if (stopped || intervalId) {
                return;
            }

            intervalId = setInterval(updateCountdown, 250);
        }

        function clearRetry() {
            if (retryTimer) {
                clearTimeout(retryTimer);
                retryTimer = null;
            }
        }

        function scheduleRetry(delayMs) {
            if (stopped || retryTimer) {
                return;
            }

            retryTimer = setTimeout(function () {
                retryTimer = null;

                if (stopped) {
                    return;
                }

                drawing = false;

                if (remainingMs() <= 0) {
                    updateCountdown();
                }
            }, delayMs);
        }

        function applyEnd(iso, serverNowIso) {
            if (!iso || stopped) {
                return false;
            }

            const nextEnd = new Date(iso).getTime();

            if (Number.isNaN(nextEnd)) {
                return false;
            }

            element.dataset.end = iso;
            endDate = nextEnd;
            syncClock(serverNowIso);

            // Only treat as a successful restart when time remains.
            if (remainingMs() <= 0) {
                return false;
            }

            clearRetry();
            drawing = false;
            drawAttempt = 0;
            updateCountdown();
            ensureInterval();

            return true;
        }

        function requestDraw() {
            const drawUrl = element.dataset.drawUrl;

            if (!drawUrl || stopped) {
                drawing = false;
                scheduleRetry(1000);
                return;
            }

            drawAttempt += 1;
            const attempt = drawAttempt;

            fetch(drawUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                cache: 'no-store',
            })
                .then(function (response) {
                    return response.json().catch(function () {
                        return {};
                    }).then(function (data) {
                        return {
                            ok: response.ok,
                            data: data || {},
                        };
                    });
                })
                .then(function (result) {
                    if (stopped || attempt !== drawAttempt) {
                        return;
                    }

                    const data = result.data || {};

                    updateWallet(data.wallet);
                    showPersonalResultNotice(data.personal_result);

                    const restarted = applyEnd(data.ends_at, data.server_now);
                    const lotteryId = data.lottery_id
                        || element.dataset.lotteryId
                        || element.closest('[data-lottery-shell]')?.getAttribute('data-lottery-shell');
                    const lotteryTitle = data.lottery_title || element.dataset.lotteryTitle || '';

                    // Show as soon as a draw/restart is confirmed — do not wait on applyEnd,
                    // which can briefly fail on very short (seconds) timers.
                    if (data.drawn || data.restarted || (data.already_drawn && restarted)) {
                        showNewRoundToast(
                            lotteryTitle,
                            data.round_label
                                ? data.round_label + ' is now live.'
                                : (data.round_number
                                    ? 'Round ' + data.round_number + ' is now live.'
                                    : 'Tickets are now on sale for the new round.'),
                            lotteryId
                        );
                    }

                    if (restarted) {
                        const liveHtmlUrl = data.live_html_url;

                        if (lotteryId && liveHtmlUrl) {
                            const shell = document.querySelector('[data-lottery-shell="' + lotteryId + '"]');
                            if (shell) {
                                return refreshLotteryShell(lotteryId, liveHtmlUrl);
                            }

                            const actions = document.querySelector('[data-lottery-show-actions="' + lotteryId + '"]');
                            if (actions) {
                                return fetch(liveHtmlUrl + (liveHtmlUrl.includes('?') ? '&' : '?') + 'view=show-actions', {
                                    headers: {
                                        'Accept': 'text/html',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    credentials: 'same-origin',
                                    cache: 'no-store',
                                })
                                    .then(function (response) {
                                        return response.text();
                                    })
                                    .then(function (html) {
                                        if (!stopped) {
                                            actions.innerHTML = html;
                                        }
                                    })
                                    .catch(function () {});
                            }
                        }

                        return;
                    }

                    // Still at zero: draw in progress, clock skew, or error — retry soon.
                    drawing = false;
                    scheduleRetry(result.ok ? 750 : 1500);
                })
                .catch(function () {
                    if (stopped || attempt !== drawAttempt) {
                        return;
                    }

                    drawing = false;
                    scheduleRetry(2000);
                });
        }

        function updateCountdown() {
            if (stopped) {
                return;
            }

            const difference = remainingMs();

            if (difference <= 0) {
                element.textContent = '00:00:00';

                // Keep the interval alive so short (seconds) timers recover after draw races.
                ensureInterval();

                if (!drawing) {
                    drawing = true;
                    clearRetry();
                    requestDraw();
                }

                return;
            }

            element.textContent = formatRemaining(difference);
        }

        updateCountdown();
        ensureInterval();
    }

    window.lotteryStartCountdown = startCountdown;
    document.querySelectorAll('.lottery-countdown').forEach(startCountdown);
});
</script>
