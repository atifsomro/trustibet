@extends('layouts.master')

@section('content')
    <section class="py-6 sm:py-8 md:py-10 lg:py-14">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Game area --}}
                <div class="lg:col-span-3 position-relative lg:sticky lg:top-0 z-3 order-2 lg:order-1">
                    <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                        <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                        <div
                            class="p-6 border-b border-brand-border bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                            <span
                                class="inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-xs sm:text-sm font-medium text-green-500">
                                <i class="fa-solid fa-gamepad"></i>
                                Now Playing
                            </span>
                            <h2 class="mt-3">{{ $game['title'] }}</h2>
                            <p class="text-gray-400 mt-2">
                                {{ $game['description'] }}
                            </p>
                        </div>

                        <div class="p-2 sm:p-6">
                            @includeIf('games.' . $slug)
                        </div>
                    </div>
                </div>

                {{-- Wallet --}}
                <div class="order-1 lg:order-2">
                    <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                        <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                        <div class="p-6">
                            <h3 class="flex items-center gap-2 text-green-500">
                                <i class="fa-solid fa-wallet"></i>
                                Wallet
                            </h3>

                            <div class="mt-4 rounded-2xl bg-brand-dark border border-green-500/20 p-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Name :</span>
                                    <span>{{ $user->name }}</span>
                                </div>
                                <div class="flex justify-between mt-3">
                                    <span class="text-gray-400">Balance</span>
                                    <span id="wallet-balance" class="font-semibold text-green-500"
                                        data-live-balance>${{ number_format($balance, 2) }}</span>
                                </div>
                                <div class="flex justify-between mt-3">
                                    <span class="text-gray-400">Bet</span>
                                    <span id="wallet-bet" class="text-orange-400">—</span>
                                </div>
                                <div class="flex justify-between mt-3">
                                    <span class="text-gray-400">Prize</span>
                                    <span id="wallet-prize" class="font-semibold text-orange-400">—</span>
                                </div>
                            </div>

                            <a href="{{ route('deposit') }}" class="btn-orange w-full mt-6">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Deposit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .swal2-game-notice {
            width: min(360px, calc(100vw - 2rem)) !important;
            padding: 1.5rem 1.25rem 1.25rem !important;
            border-radius: 1.25rem !important;
            border: 1px solid rgba(56, 189, 248, 0.22) !important;
            background: linear-gradient(180deg, #152238 0%, #0f172a 100%) !important;
            box-shadow: 0 22px 48px rgba(0, 0, 0, 0.5) !important;
        }

        .swal2-game-notice.swal2-game-notice--warning {
            border-color: rgba(249, 115, 22, 0.3) !important;
        }

        .swal2-game-notice.swal2-game-notice--error {
            border-color: rgba(239, 68, 68, 0.3) !important;
        }

        .swal2-game-notice.swal2-game-notice--success {
            border-color: rgba(34, 197, 94, 0.3) !important;
        }

        .swal2-game-notice .swal2-icon {
            margin: 0 auto 0.75rem !important;
            border: 0 !important;
            width: auto !important;
            height: auto !important;
            background: transparent !important;
        }

        .swal2-game-notice .swal2-icon .swal2-icon-content,
        .swal2-game-notice .game-notice-emoji {
            font-size: 2.75rem !important;
            line-height: 1 !important;
        }

        .swal2-game-notice .swal2-title {
            margin: 0 0 0.45rem !important;
            padding: 0 !important;
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            color: #fff !important;
            line-height: 1.3 !important;
        }

        .swal2-game-notice .swal2-html-container {
            margin: 0 0 1.15rem !important;
            padding: 0 !important;
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
            color: rgba(255, 255, 255, 0.72) !important;
        }

        .swal2-game-notice .swal2-actions {
            margin: 0 !important;
            width: 100%;
        }

        .swal2-game-notice .swal2-confirm {
            margin: 0 !important;
            width: 100%;
            padding: 0.7rem 1.35rem !important;
            border: 0 !important;
            border-radius: 0.85rem !important;
            background: linear-gradient(90deg, #22c55e, #f97316) !important;
            box-shadow: 0 10px 22px rgba(34, 197, 94, 0.22) !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #fff !important;
        }

        .swal2-game-notice .swal2-confirm:focus {
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.28) !important;
        }

        .swal2-game-backdrop {
            background: rgba(2, 6, 23, 0.72) !important;
            backdrop-filter: blur(4px);
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.showGameNotice = function(options) {
            const opts = typeof options === 'string' ?
                {
                    message: options
                } :
                (options || {});

            const type = opts.type || 'warning';
            const defaults = {
                warning: {
                    title: 'Hold on',
                    emoji: '🎯',
                    confirm: 'Got it'
                },
                error: {
                    title: 'Unable to continue',
                    emoji: '😔',
                    confirm: 'Try again'
                },
                success: {
                    title: 'Nice!',
                    emoji: '🎉',
                    confirm: 'Continue'
                },
                info: {
                    title: 'Heads up',
                    emoji: '✨',
                    confirm: 'OK'
                },
            };
            const preset = defaults[type] || defaults.warning;

            if (typeof Swal === 'undefined') {
                window.alert(opts.message || opts.title || 'Notice');
                return Promise.resolve();
            }

            return Swal.fire({
                iconHtml: `<span class="game-notice-emoji">${opts.emoji || preset.emoji}</span>`,
                title: opts.title || preset.title,
                html: opts.message || '',
                confirmButtonText: opts.confirmText || preset.confirm,
                buttonsStyling: false,
                width: 360,
                background: '#0f172a',
                color: '#fff',
                backdrop: true,
                customClass: {
                    popup: `swal2-game-notice swal2-game-notice--${type}`,
                    confirmButton: 'swal2-confirm',
                    backdrop: 'swal2-game-backdrop',
                },
            });
        };
    </script>
@endpush
