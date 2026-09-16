<!DOCTYPE html>
<html lang="en">

<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ClaimRise Mining | ClaimRise UK – Secure Crypto Mining Platform')</title>
    <meta name="title" content="ClaimRise Mining | ClaimRise UK – Secure Crypto Mining Platform">
    <meta name="description"
        content="Discover ClaimRise Mining at ClaimRise.uk. Start secure and efficient cryptocurrency mining with a user-friendly platform, real-time insights, and reliable performance.">
    <meta name="keywords"
        content="claimrise, online casino, claimrise mining, live casino, casino games, online betting, claimrise uk, betting platform, slots, roulette, blackjack, live dealer, aviator game, color trading, dice game, lucky wheel, betting bonuses, secure betting, fast withdrawals">
    <meta name="robots" content="index, follow">
    <meta name="author" content="TrustiBet">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/brand/favicon.png') }}">
    <!-- PNG Favicon (Recommended) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/brand/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/brand/favicon.png') }}">
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/brand/apple-touch-icon.png') }}">
    <!-- Android Icon -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/brand/android-chrome-192x192.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">


    {{-- Page Specific CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notifications.js'])
    @include('partials.purchase-alert-styles')
    @stack('styles')

</head>

<body>

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
    @include('components.back-to-top')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    {{-- Page Specific JS --}}
    @stack('scripts')
    <script src="{{ asset('js/purchase.js') }}?v={{ filemtime(public_path('js/purchase.js')) }}"></script>
    <script>
        const backToTop = document.getElementById("backToTop");
        window.addEventListener("scroll", () => {
            if (window.scrollY > 100) {
                backToTop.classList.remove("opacity-0", "translate-y-20");
                backToTop.classList.add("opacity-100", "translate-y-0");
            } else {
                backToTop.classList.add("opacity-0", "translate-y-20");
                backToTop.classList.remove("opacity-100", "translate-y-0");
            }
        });
        backToTop.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    </script>
    @include('components.support')

    @if (session('success') && empty($skipFlashSwal))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: @json(session('success')),
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    @if (session('warning') && empty($skipFlashSwal))
        <style>
            .swal2-lottery-warning {
                width: min(340px, calc(100vw - 2rem)) !important;
                padding: 1.25rem 1.15rem 1.1rem !important;
                border-radius: 1rem !important;
                border: 1px solid rgba(249, 115, 22, 0.28) !important;
                background: linear-gradient(180deg, #132033 0%, #0f172a 100%) !important;
                box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45) !important;
            }

            .swal2-lottery-warning .swal2-icon {
                margin: 0 auto 0.65rem !important;
                width: 2.75rem !important;
                height: 2.75rem !important;
                border-width: 2px !important;
                border-color: rgba(249, 115, 22, 0.55) !important;
                color: #fb923c !important;
            }

            .swal2-lottery-warning .swal2-icon .swal2-icon-content {
                font-size: 1.35rem !important;
                color: #fb923c !important;
            }

            .swal2-lottery-warning .swal2-title {
                margin: 0 0 0.4rem !important;
                padding: 0 !important;
                font-size: 1.05rem !important;
                font-weight: 700 !important;
                color: #fff !important;
                line-height: 1.3 !important;
            }

            .swal2-lottery-warning .swal2-html-container {
                margin: 0 0 1rem !important;
                padding: 0 !important;
                font-size: 0.85rem !important;
                line-height: 1.45 !important;
                color: rgba(255, 255, 255, 0.72) !important;
            }

            .swal2-lottery-warning .swal2-actions {
                margin: 0 !important;
            }

            .swal2-lottery-warning .swal2-confirm {
                margin: 0 !important;
                padding: 0.55rem 1.35rem !important;
                border: 0 !important;
                border-radius: 0.75rem !important;
                background: linear-gradient(90deg, #22c55e, #f97316) !important;
                box-shadow: 0 8px 18px rgba(34, 197, 94, 0.22) !important;
                font-size: 0.85rem !important;
                font-weight: 600 !important;
            }

            .swal2-lottery-warning .swal2-confirm:focus {
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.25) !important;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Insufficient Balance',
                    text: @json(session('warning')),
                    confirmButtonText: 'Deposit Now',
                    buttonsStyling: false,
                    width: 340,
                    padding: '1.25rem',
                    background: '#0f172a',
                    color: '#fff',
                    customClass: {
                        popup: 'swal2-lottery-warning',
                        confirmButton: 'swal2-confirm'
                    }
                });
            });
        </script>
    @endif

    @if ($errors->any() && empty($skipFlashSwal))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `
                    <ul class="text-center">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `
                });
            });
        </script>
    @endif
</body>

</html>
