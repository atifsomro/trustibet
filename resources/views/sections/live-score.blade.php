<section class="live_activity py-10">
    <div class="container">
        <div class="sec_heading text-center">
            <span class="sec_subtitle">
                Live Activity
            </span>
            <h2 class="mt-4">
                Recent Platform Activity
            </h2>
            <p class="mt-4 max-w-2xl mx-auto opacity-70">
                View the latest deposits, withdrawals and winning activities across the platform.
            </p>
        </div>
        <div class="mt-4 md:mt-6 lg:mt-10 rounded-3xl border border-brand-border bg-brand-surface overflow-hidden">
            <div class="flex items-center justify-between px-3 py-3 lg:px-6 lg:py-5 border-b border-brand-border bg-brand-dark">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <h5>
                        Live Activity Feed
                    </h5>
                </div>
                <span class="text-sm opacity-60">
                    Auto Refresh
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-[10px] md:text-lg">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-3 px-3 lg:py-5 lg:px-6 text-left">
                                User ID
                            </th>
                            <th class="py-3 px-3 lg:py-5 lg:px-6 text-left">
                                Activity
                            </th>
                            <th class="py-3 px-3 lg:py-5 lg:px-6 text-left">
                                Amount
                            </th>
                            <th class="py-3 px-3 lg:py-5 lg:px-6 text-left">
                                Time
                            </th>
                        </tr>
                    </thead>
                    <tbody id="activityTable">
                        {{-- Backend Loop --}}
                        {{-- @foreach ($activities as $activity) --}}
                        <tr class="border-b border-brand-border hover:bg-brand-dark duration-300">
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-medium">
                                USR****248
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-green-500/15 px-4 py-2 text-green-500">
                                    <i class="fa-solid fa-arrow-down"></i>
                                    Deposit
                                </span>
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-semibold">
                                $850
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 opacity-70">
                                2 min ago
                            </td>
                        </tr>
                        <tr class="border-b border-brand-border hover:bg-brand-dark duration-300">
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-medium">
                                USR****982
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-red-500/15 px-4 py-2 text-red-500">
                                    <i class="fa-solid fa-arrow-up"></i>
                                    Withdrawal
                                </span>
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-semibold">
                                $1,250
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 opacity-70">
                                5 min ago
                            </td>
                        </tr>
                        <tr class="border-b border-brand-border hover:bg-brand-dark duration-300">
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-medium">
                                USR****641
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-scrach-lock/15 px-4 py-2 text-scrach-lock">
                                    <i class="fa-solid fa-trophy"></i>
                                    Winner
                                </span>
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-semibold">
                                $5,000
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 opacity-70">
                                11 min ago
                            </td>
                        </tr>
                        <tr class="border-b border-brand-border hover:bg-brand-dark duration-300">
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-medium">
                                USR****120
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-green-500/15 px-4 py-2 text-green-500">
                                    <i class="fa-solid fa-arrow-down"></i>
                                    Deposit
                                </span>
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-semibold">
                                $3,400
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 opacity-70">
                                18 min ago
                            </td>
                        </tr>
                        <tr class="hover:bg-brand-dark duration-300">
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-medium">
                                USR****763
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-red-500/15 px-4 py-2 text-red-500">
                                    <i class="fa-solid fa-arrow-up"></i>
                                    Withdrawal
                                </span>
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 font-semibold">
                                $2,700
                            </td>
                            <td class="px-3 py-3 lg:px-6 lg:py-5 opacity-70">
                                25 min ago
                            </td>
                        </tr>
                        {{-- @endforeach --}}
                    </tbody>
                </table>
                <div id="activityPagination" class="mt-10 px-3"></div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        const perPage = 5;
        let currentPage = 1;

        const users = [
            "USR****248",
            "USR****982",
            "USR****641",
            "USR****120",
            "USR****763",
            "USR****489",
            "USR****157",
            "USR****611",
            "USR****399",
            "USR****731",
            "USR****510",
            "USR****842",
            "USR****995",
            "USR****456",
            "USR****873",
            "USR****382",
            "USR****267",
            "USR****718",
            "USR****614",
            "USR****901"
        ];

        const types = [
            "deposit",
            "withdrawal",
            "winner"
        ];

        const activities = [{
                user_id: "USR****248",
                type: "deposit",
                amount: 850,
                time: "2 min ago"
            },
            {
                user_id: "USR****982",
                type: "withdrawal",
                amount: 1250,
                time: "5 min ago"
            },
            {
                user_id: "USR****641",
                type: "winner",
                amount: 5000,
                time: "11 min ago"
            },
            {
                user_id: "USR****120",
                type: "deposit",
                amount: 3400,
                time: "18 min ago"
            },
            {
                user_id: "USR****763",
                type: "withdrawal",
                amount: 2700,
                time: "25 min ago"
            },

            {
                user_id: "USR****489",
                type: "winner",
                amount: 9200,
                time: "33 min ago"
            },
            {
                user_id: "USR****157",
                type: "deposit",
                amount: 780,
                time: "41 min ago"
            },
            {
                user_id: "USR****611",
                type: "withdrawal",
                amount: 1100,
                time: "55 min ago"
            },
            {
                user_id: "USR****399",
                type: "deposit",
                amount: 4600,
                time: "1 hour ago"
            },
            {
                user_id: "USR****731",
                type: "winner",
                amount: 12000,
                time: "1 hour ago"
            },

            {
                user_id: "USR****510",
                type: "withdrawal",
                amount: 1950,
                time: "2 hours ago"
            },
            {
                user_id: "USR****842",
                type: "deposit",
                amount: 630,
                time: "2 hours ago"
            },
            {
                user_id: "USR****995",
                type: "winner",
                amount: 8700,
                time: "3 hours ago"
            },
            {
                user_id: "USR****456",
                type: "deposit",
                amount: 4100,
                time: "4 hours ago"
            },
            {
                user_id: "USR****873",
                type: "withdrawal",
                amount: 1500,
                time: "5 hours ago"
            },

            {
                user_id: "USR****382",
                type: "winner",
                amount: 14300,
                time: "6 hours ago"
            },
            {
                user_id: "USR****267",
                type: "deposit",
                amount: 980,
                time: "7 hours ago"
            },
            {
                user_id: "USR****718",
                type: "withdrawal",
                amount: 2300,
                time: "8 hours ago"
            },
            {
                user_id: "USR****614",
                type: "deposit",
                amount: 5600,
                time: "10 hours ago"
            },
            {
                user_id: "USR****901",
                type: "winner",
                amount: 25000,
                time: "12 hours ago"
            }
        ];

        const table = document.getElementById("activityTable");
        const storageKey = "trustibet_live_activity";
        if (localStorage.getItem(storageKey)) {
            activities.splice(
                0,
                activities.length,
                ...JSON.parse(localStorage.getItem(storageKey))
            );
        }

        function saveActivities() {
            localStorage.setItem(
                storageKey,
                JSON.stringify(activities)
            );
        }

        function badge(type) {

            if (type === "deposit") {
                return `
        <span class="inline-flex items-center gap-2 rounded-full bg-green-500/15 px-4 py-2 text-green-500">
            <i class="fa-solid fa-arrow-down"></i>
            Deposit
        </span>`;
            }

            if (type === "withdrawal") {
                return `
        <span class="inline-flex items-center gap-2 rounded-full bg-red-500/15 px-4 py-2 text-red-500">
            <i class="fa-solid fa-arrow-up"></i>
            Withdrawal
        </span>`;
            }

            return `
    <span class="inline-flex items-center gap-2 rounded-full bg-scrach-lock/15 px-4 py-2 text-scrach-lock">
        <i class="fa-solid fa-trophy"></i>
        Winner
    </span>`;
        }

        function randomActivity() {
            return {
                user_id: users[Math.floor(Math.random() * users.length)],
                type: types[Math.floor(Math.random() * types.length)],
                amount: Math.floor(Math.random() * 45000) + 500,
                time: "Just now"
            };
        }

        function updateTimes() {

            activities.forEach((item, index) => {

                if (index == 0) {

                    item.time = "Just now";

                } else if (index == 1) {

                    item.time = "1 min ago";

                } else if (index < 60) {

                    item.time = index + " min ago";

                } else {

                    item.time = Math.floor(index / 60) + " hour ago";

                }

            });

        }

        function renderTable(data) {

            table.innerHTML = "";

            data.forEach(item => {

                table.innerHTML += `

        <tr class="border-b border-brand-border hover:bg-brand-dark duration-300">

            <td class="px-3 py-3 lg:px-6 lg:py-5 font-medium">
                ${item.user_id}
            </td>

            <td class="px-3 py-3 lg:px-6 lg:py-5">
                ${badge(item.type)}
            </td>

            <td class="px-3 py-3 lg:px-6 lg:py-5 font-semibold">
                $${item.amount.toLocaleString()}
            </td>

            <td class="px-3 py-3 lg:px-6 lg:py-5 opacity-70">
                ${item.time}
            </td>

        </tr>

        `;

            });

        }

        function paginate(page) {
            currentPage = page;
            const start = (page - 1) * perPage;
            const end = start + perPage;
            renderTable(activities.slice(start, end));
            renderPagination();
        }

        function renderPagination() {

            const totalPages = Math.ceil(activities.length / perPage);

            document.getElementById("activityPagination").innerHTML = `
    
    <div class="flex items-center justify-between pb-3">
        <button
            onclick="paginate(currentPage - 1)"
            ${currentPage === 1 ? "disabled" : ""}
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-brand-border bg-brand-dark hover:border-brand-primary disabled:opacity-40 disabled:cursor-not-allowed">
            Previous
        </button>
        <span class="text-sm opacity-70">
            Page ${currentPage} of ${totalPages}
        </span>
        <button
            onclick="paginate(currentPage + 1)"
            ${currentPage === totalPages ? "disabled" : ""}
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-brand-border bg-brand-dark hover:border-brand-primary disabled:opacity-40 disabled:cursor-not-allowed">
            Next
        </button>

    </div>

    `;
        }

        function addLiveActivity() {
            activities.unshift(randomActivity());
            saveActivities();

            const totalPages = Math.ceil(activities.length / perPage);
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            paginate(currentPage);
        }

        paginate(1);

        setInterval(() => {
            addLiveActivity();
            updateTimes();
        }, 10000);
    </script>
@endpush
