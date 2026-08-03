<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
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

    @if (session('success'))
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

    @if ($errors->any())
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
