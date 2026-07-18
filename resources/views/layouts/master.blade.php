<!DOCTYPE html>
<html lang="en">

<head>

    @include('partials.head')

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
</body>

</html>
