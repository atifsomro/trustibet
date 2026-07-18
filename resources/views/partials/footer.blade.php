<footer class="footer border-t border-brand-border bg-brand-surface/40 backdrop-blur-sm py-6 md:py-10 relative overflow-hidden">
    <div class="container relative z-10">
        <div class="footer__wrapper flex flex-col gap-4 md:gap-10 lg:flex-row lg:items-center lg:justify-between">
            {{-- Brand --}}
            <div class="footer__brand max-w-md">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('/images/brand/logo.png') }}"
                        class="w-32 md:w-36 lg:w-40 h-12 md:h-14 object-contain"
                        alt="Logo">
                </a>
                <p class="mt-4 md:mt-5 leading-8 text-gray-400">
                    Join a trusted platform built for secure participation,
                    transparent rewards and a smooth experience every day.
                </p>
            </div>
            {{-- Quick Links --}}
            <div class="footer__links">
                <h5 class="font-semibold text-white">
                    Quick Links
                </h5>
                <div class="mt-3 md:mt-5 flex flex-wrap justify-start gap-x-4 gap-y-2 md:gap-x-8 md:gap-y-4 lg:justify-center">
                    <a href="{{ url('/about') }}"
                        class="text-gray-400 transition-all duration-300 hover:text-green-500">
                        About Us
                    </a>

                    <a href="{{ url('/contact') }}"
                        class="text-gray-400 transition-all duration-300 hover:text-green-500">
                        Contact Us
                    </a>

                    <a href="{{ route('privacy.policy') }}"
                        class="text-gray-400 transition-all duration-300 hover:text-green-500">
                        Privacy Policy
                    </a>

                    <a href="{{ route('terms.conditions') }}"
                        class="text-gray-400 transition-all duration-300 hover:text-green-500">
                        Terms & Conditions
                    </a>

                </div>
            </div>
            {{-- Social --}}
            <div class="footer__social">
                <h5 class="font-semibold text-white">
                    Follow Us
                </h5>
                <div class="mt-3 md:mt-5 flex gap-4">
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border bg-brand-dark transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:bg-green-500/10 hover:text-green-500">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border bg-brand-dark transition-all duration-300 hover:-translate-y-1 hover:border-brand-primary hover:bg-brand-primary/10 hover:text-brand-primary">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border bg-brand-dark transition-all duration-300 hover:-translate-y-1 hover:border-scrach-lock hover:bg-scrach-lock/10 hover:text-scrach-lock">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border bg-brand-dark transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:bg-green-500/10 hover:text-green-500">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>
        {{-- Bottom --}}
        <div class="footer__bottom mt-6 md:mt-10 border-t border-brand-border pt-6 text-center">
            <p class="text-gray-500">
                © {{ date('Y') }} Trustibet.
                All rights reserved.
            </p>

        </div>

    </div>

</footer>