<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const triggered = {};
    const params = new URLSearchParams(window.location.search);

    if (params.get('new_round') === '1') {
        params.delete('new_round');
        const query = params.toString();
        window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
    }

    function jsonHeaders() {
        return {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf || '',
        };
    }

    function triggerDraw(url, reloadOnSuccess) {
        if (!url || triggered[url]) {
            return;
        }

        triggered[url] = true;

        fetch(url, {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.data && result.data.drawn && reloadOnSuccess !== false) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('new_round', '1');
                    window.location.href = url.toString();
                }
            })
            .catch(function () {
                triggered[url] = false;
            });
    }

    const drawAllUrl = @json(route('lotteries.draw-due.all'));
    triggerDraw(drawAllUrl, true);

    document.querySelectorAll('.lottery-countdown').forEach(function (element) {
        const endDate = new Date(element.dataset.end).getTime();
        const drawUrl = element.dataset.drawUrl;
        let intervalId = null;

        function updateCountdown() {
            const difference = endDate - new Date().getTime();

            if (difference <= 0) {
                element.textContent = '00:00:00';
                triggerDraw(drawUrl, true);
                if (intervalId) {
                    clearInterval(intervalId);
                }
                return;
            }

            const hours = Math.floor(difference / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);

            element.textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }

        updateCountdown();
        intervalId = setInterval(updateCountdown, 1000);
    });
});
</script>
