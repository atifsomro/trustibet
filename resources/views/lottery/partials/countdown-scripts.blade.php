<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    let reloadScheduled = false;

    if (params.get('new_round') === '1') {
        params.delete('new_round');
        const query = params.toString();
        window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function scheduleSoftReload(delayMs) {
        if (reloadScheduled) {
            return;
        }

        reloadScheduled = true;

        setTimeout(function () {
            const url = new URL(window.location.href);
            url.searchParams.set('new_round', '1');
            window.location.href = url.toString();
        }, delayMs || 2000);
    }

    function formatRemaining(difference) {
        const hours = Math.floor(difference / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        return String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');
    }

    function startCountdown(element) {
        let endDate = new Date(element.dataset.end).getTime();
        let intervalId = null;
        let drawing = false;

        function applyEnd(iso) {
            if (!iso) {
                return false;
            }

            element.dataset.end = iso;
            endDate = new Date(iso).getTime();
            drawing = false;
            updateCountdown();

            if (!intervalId) {
                intervalId = setInterval(updateCountdown, 1000);
            }

            return true;
        }

        function requestDraw() {
            const drawUrl = element.dataset.drawUrl;

            if (!drawUrl) {
                scheduleSoftReload(3000);
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
                    if (data && data.ends_at && applyEnd(data.ends_at)) {
                        // New round is live — refresh the rest of the page shortly.
                        scheduleSoftReload(1500);
                        return;
                    }

                    scheduleSoftReload(3000);
                })
                .catch(function () {
                    scheduleSoftReload(5000);
                });
        }

        function updateCountdown() {
            const difference = endDate - new Date().getTime();

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

        if (endDate - new Date().getTime() > 0) {
            intervalId = setInterval(updateCountdown, 1000);
        }
    }

    document.querySelectorAll('.lottery-countdown').forEach(startCountdown);
});
</script>
