<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get('new_round') === '1') {
        params.delete('new_round');
        const query = params.toString();
        window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
        showNewRoundBanner();
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
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

    function showNewRoundBanner(message) {
        let banner = document.getElementById('newRoundBanner');

        if (!banner) {
            const host = document.getElementById('lotteryLiveNotices') || document.querySelector('.container');
            if (!host) {
                return;
            }

            banner = document.createElement('div');
            banner.id = 'newRoundBanner';
            banner.className = 'mx-auto mt-8 max-w-3xl rounded-2xl border border-green-500/20 bg-green-500/10 px-5 py-4 text-sm text-green-400';
            host.prepend(banner);
        }

        banner.innerHTML =
            '<strong class="block">New round started.</strong>' +
            '<span class="mt-1 block opacity-80">' +
            (message || 'The previous draw is complete. Tickets are now on sale for the new round.') +
            '</span>';
        banner.hidden = false;
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
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                const current = document.querySelector('[data-lottery-shell="' + lotteryId + '"]');
                if (!current) {
                    return;
                }

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
        let drawing = false;

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

        function applyEnd(iso, serverNowIso) {
            if (!iso) {
                return false;
            }

            element.dataset.end = iso;
            endDate = new Date(iso).getTime();
            syncClock(serverNowIso);
            drawing = false;
            updateCountdown();

            if (!intervalId) {
                intervalId = setInterval(updateCountdown, 250);
            }

            return true;
        }

        function requestDraw() {
            const drawUrl = element.dataset.drawUrl;

            if (!drawUrl) {
                return;
            }

            fetch(drawUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(function (response) {
                    return response.json().catch(function () {
                        return {};
                    });
                })
                .then(function (data) {
                    if (!data) {
                        drawing = false;
                        return;
                    }

                    updateWallet(data.wallet);
                    showPersonalResultNotice(data.personal_result);

                    if (data.ends_at) {
                        applyEnd(data.ends_at, data.server_now);
                    }

                    if (data.drawn || data.already_drawn || data.restarted) {
                        showNewRoundBanner();
                    }

                    const lotteryId = data.lottery_id
                        || element.dataset.lotteryId
                        || element.closest('[data-lottery-shell]')?.getAttribute('data-lottery-shell');
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
                            })
                                .then(function (response) {
                                    return response.text();
                                })
                                .then(function (html) {
                                    actions.innerHTML = html;
                                })
                                .catch(function () {});
                        }
                    }
                })
                .catch(function () {
                    drawing = false;
                    setTimeout(function () {
                        if (endDate - nowMs() <= 0) {
                            drawing = true;
                            requestDraw();
                        }
                    }, 3000);
                });
        }

        function updateCountdown() {
            const difference = endDate - nowMs();

            if (difference <= 0) {
                element.textContent = '00:00:00';

                if (intervalId) {
                    clearInterval(intervalId);
                    intervalId = null;
                }

                if (!drawing) {
                    drawing = true;
                    requestDraw();
                }

                return;
            }

            element.textContent = formatRemaining(difference);
        }

        updateCountdown();

        if (endDate - nowMs() > 0) {
            intervalId = setInterval(updateCountdown, 250);
        }
    }

    window.lotteryStartCountdown = startCountdown;
    document.querySelectorAll('.lottery-countdown').forEach(startCountdown);
});
</script>
