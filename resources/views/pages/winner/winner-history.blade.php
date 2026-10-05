@extends('layouts.master')

@section('content')
    <section class="py-8">
        <div class="container">

            {{-- Heading --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
                <div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-medium uppercase tracking-[3px] text-green-500">
                        <i class="fa-solid fa-trophy"></i>
                        TrustiBet
                    </span>
                    <h2 class="mt-3">
                        Leader Board
                    </h2>
                    <p class="mt-2 opacity-70">
                        Explore previous lucky draw winners and prize history.
                    </p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div
                    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-[0_0_30px_rgba(34,197,94,.18)]">
                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                    <div class="p-6">
                        <p class="opacity-70">Total Winners</p>
                        <span class="text-xl md:text-2xl lg:text-4xl font-extrabold text-green-500">2,580</span>
                    </div>
                </div>
                <div
                    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-orange-500 hover:shadow-[0_0_30px_rgba(249,115,22,.18)]">
                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                    <div class="p-6">
                        <p class="opacity-70">Total Prize Paid</p>
                        <span class="text-xl md:text-2xl lg:text-4xl text-orange-400 font-extrabold">Rs.12,50,000</span>
                    </div>
                </div>
                <div
                    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-[0_0_30px_rgba(34,197,94,.18)]">
                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                    <div class="p-6">
                        <p class="opacity-70">Latest Winner</p>
                        <span class="text-xl md:text-2xl lg:text-4xl text-green-500 font-extrabold">Today</span>
                    </div>
                </div>
            </div>

            {{-- Table card --}}
            <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="p-3 md:p-6 border-b border-brand-border">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <input type="text" placeholder="Search by winner or draw id..."
                            class="flex-1 rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none focus:border-green-500">
                        <select
                            class="rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none focus:border-green-500">
                            <option>All Prizes</option>
                            <option>Rs.100</option>
                            <option>Rs.500</option>
                            <option>Rs.1,000</option>
                            <option>Rs.5,000+</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-[12px] md:text-base lg:text-xl">
                        <thead class="border-b border-brand-border bg-gradient-to-r from-green-500/10 to-orange-500/10">
                            <tr>
                                <th class="p-3 md:p-5 text-left text-green-500">Winner</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Prize</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Winning Number</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Draw ID</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Date</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Status</th>
                                <th class="p-3 md:p-5 text-left text-green-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-[12px] md:text-base lg:text-xl">

                            <tr class="border-b border-brand-border hover:bg-brand-dark duration-300">
                                <td class="p-3 md:p-5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('images/profile/avatar.png') }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-green-500/50">
                                        <div>
                                            <h6>Tig***King</h6>
                                            <small class="opacity-60">Pakistan</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 md:p-5 text-orange-400 font-semibold">
                                    Rs.50,000
                                </td>
                                <td class="p-3 md:p-5">
                                    #548921
                                </td>
                                <td class="p-3 md:p-5">
                                    DRAW-2054
                                </td>
                                <td class="p-3 md:p-5">
                                    05 Jul 2026
                                </td>
                                <td class="p-3 md:p-5">
                                    <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-500 text-sm">
                                        Paid
                                    </span>
                                </td>
                                <td class="p-3 md:p-5">
                                    <button class="viewWinner btn-orange !w-auto px-4 text-sm" data-name="Tig***King"
                                        data-avatar="{{ asset('images/profile/avatar.png') }}" data-prize="Rs.50,000"
                                        data-number="#548921" data-draw="DRAW-2054" data-date="05 Jul 2026"
                                        data-status="Paid">
                                        <i class="fa-solid fa-eye mr-2"></i>
                                        View
                                    </button>
                                </td>
                            </tr>

                            <tr class="border-b border-brand-border hover:bg-brand-dark duration-300">
                                <td class="p-3 md:p-5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('images/profile/avatar.png') }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-green-500/50">
                                        <div>
                                            <h6>Ali***786</h6>
                                            <small class="opacity-60">Pakistan</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 md:p-5 text-orange-400 font-semibold">
                                    Rs.10,000
                                </td>
                                <td class="p-3 md:p-5">
                                    #784512
                                </td>
                                <td class="p-3 md:p-5">
                                    DRAW-2053
                                </td>
                                <td class="p-3 md:p-5">
                                    04 Jul 2026
                                </td>
                                <td class="p-3 md:p-5">
                                    <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-500 text-sm">
                                        Paid
                                    </span>
                                </td>
                                <td class="p-3 md:p-5">
                                    <button class="viewWinner btn-orange !w-auto px-4 text-sm" data-name="Tig***King"
                                        data-avatar="{{ asset('images/profile/avatar.png') }}" data-prize="Rs.50,000"
                                        data-number="#548921" data-draw="DRAW-2054" data-date="05 Jul 2026"
                                        data-status="Paid">
                                        <i class="fa-solid fa-eye mr-2"></i>
                                        View
                                    </button>
                                </td>
                            </tr>

                            <tr class="hover:bg-brand-dark duration-300">
                                <td class="p-3 md:p-5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('images/profile/avatar.png') }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-green-500/50">
                                        <div>
                                            <h6>Ham***112</h6>
                                            <small class="opacity-60">Pakistan</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 md:p-5 text-orange-400 font-semibold">
                                    Rs.5,000
                                </td>
                                <td class="p-3 md:p-5">
                                    #665412
                                </td>
                                <td class="p-3 md:p-5">
                                    DRAW-2052
                                </td>
                                <td class="p-3 md:p-5">
                                    03 Jul 2026
                                </td>
                                <td class="p-3 md:p-5">
                                    <span class="px-3 py-1 rounded-full bg-orange-500/20 text-orange-400 text-sm">
                                        Processing
                                    </span>
                                </td>
                                <td class="p-3 md:p-5">
                                    <button class="viewWinner btn-orange !w-auto px-4 text-sm" data-name="Tig***King"
                                        data-avatar="{{ asset('images/profile/avatar.png') }}" data-prize="Rs.50,000"
                                        data-number="#548921" data-draw="DRAW-2054" data-date="05 Jul 2026"
                                        data-status="Paid">
                                        <i class="fa-solid fa-eye mr-2"></i>
                                        View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center gap-3 mt-8">
                <button class="btn-secondary transition hover:!border-green-500 hover:!text-green-500">
                    Previous
                </button>
                <button class="btn-secondary transition hover:!border-green-500 hover:!text-green-500">
                    Next
                </button>
            </div>
        </div>
    </section>

    {{-- Winner modal popup --}}
    <div id="winnerModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 p-5">
        <div
            class="relative w-full max-w-lg overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.18),0_0_70px_rgba(249,115,22,.10)]">

            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

            <div class="p-8 relative">
                <button id="closeWinnerModal"
                    class="absolute right-5 top-5 text-2xl opacity-70 hover:opacity-100 hover:text-orange-400 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="text-center">
                    <img id="winnerAvatar" src=""
                        class="w-28 h-28 rounded-full object-cover mx-auto border-4 border-green-500 shadow-[0_0_25px_rgba(34,197,94,.3)]">
                    <h3 id="winnerName" class="mt-5"></h3>
                    <span id="winnerStatus" class="inline-block mt-3 px-4 py-2 rounded-full text-sm"></span>
                </div>

                <div class="grid grid-cols-2 gap-5 mt-8">
                    <div class="rounded-2xl bg-brand-dark border border-orange-500/20 p-4">
                        <p class="opacity-60 text-sm">
                            Prize
                        </p>
                        <h5 id="winnerPrize" class="text-orange-400 mt-2"></h5>
                    </div>
                    <div class="rounded-2xl bg-brand-dark border border-green-500/20 p-4">
                        <p class="opacity-60 text-sm">
                            Winning Number
                        </p>
                        <h5 id="winnerNumber" class="text-green-500 mt-2"></h5>
                    </div>
                    <div class="rounded-2xl bg-brand-dark border border-green-500/20 p-4">
                        <p class="opacity-60 text-sm">
                            Draw ID
                        </p>
                        <h5 id="winnerDraw" class="mt-2"></h5>
                    </div>
                    <div class="rounded-2xl bg-brand-dark border border-orange-500/20 p-4">
                        <p class="opacity-60 text-sm">
                            Winning Date
                        </p>
                        <h5 id="winnerDate" class="mt-2"></h5>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="font-semibold bg-gradient-to-r from-green-500 to-orange-500 bg-clip-text text-transparent">
                        🎉 Congratulations to our lucky winner!
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
