@extends('layouts.master')

@section('content')
    <section class="py-14">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Game Area --}}
                <div class="lg:col-span-3 position-relative lg:sticky lg:top-0 z-3 order-2 lg:order-1"">
                    <div class="bg-brand-surface border border-brand-border rounded-2xl overflow-hidden">
                        {{-- Game Header --}}
                        <div class="p-6 border-b border-brand-border">
                            <h2>{{ $game['title'] }}</h2>
                            <p class="text-gray-400 mt-2">
                                {{ $game['description'] }}
                            </p>
                        </div>
                        {{-- Game Content --}}
                        <div class="p-6">
                            @includeIf('games.' . $slug)
                        </div>
                    </div>
                </div>
                {{-- Right Panel --}}
                <div class="order-1 lg:order-2">
                    <div class="bg-brand-surface border border-brand-border rounded-2xl p-6">
                        <h3>Wallet</h3>
                        <div class="mt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Name :</span>
                                <span>Player Name</span>
                            </div>
                            <div class="flex justify-between mt-3">
                                <span class="text-gray-400">Balance</span>
                                <span>$100</span>
                            </div>
                            <div class="flex justify-between mt-3">
                                <span class="text-gray-400">Bet</span>
                                <span>$10</span>
                            </div>
                            <div class="flex justify-between mt-3">
                                <span class="text-gray-400">Prize</span>
                                <span>---</span>
                            </div>
                        </div>
                        <a href="{{ route('deposit') }}" class="btn-primary w-full mt-6">
                            Deposit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
