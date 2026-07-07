<footer class="footer border-t border-brand-border py-10">
    <div class="container">
        <div class="footer__wrapper flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between">
            {{-- Brand --}}
            <div class="footer__brand max-w-md">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('/images/brand/logo.png') }}"
                        class="w-32 md:w-36 lg:w-40 h-12 md:h-14 object-contain" alt="Logo">
                </a>
                <p class="mt-4">
                    Experience exciting casino games with a secure, fast, and
                    modern gaming platform designed for endless entertainment.
                </p>
            </div>
            {{-- Quick Links --}}
            <div class="footer__links">
                <h5>
                    Quick Links
                </h5>
                <div class="mt-4 flex flex-wrap justify-start gap-6 lg:justify-center">
                    <a href="{{ url('/about') }}" class="hover:text-brand-primary">
                        About Us
                    </a>
                    <a href="{{ url('/contact') }}" class="hover:text-brand-primary">
                        Contact Us
                    </a>
                    <a href="{{ route('privacy.policy') }}" class="hover:text-brand-primary">
                        Privacy Policy
                    </a>

                    <a href="{{ route('terms.conditions') }}" class="hover:text-brand-primary">
                        Terms & Conditions
                    </a>
                </div>
            </div>
            {{-- Social --}}
            <div class="footer__social">
                <h5>
                    Follow Us
                </h5>
                <div class="mt-4 flex gap-4">
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border hover:border-brand-primary">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border hover:border-brand-primary">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border hover:border-brand-primary">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-border hover:border-brand-primary">
                        <i class="fa-brands fa-discord"></i>
                    </a>
                </div>
            </div>
        </div>
        {{-- Bottom --}}
        <div class="footer__bottom mt-10 border-t border-brand-border pt-6 text-center">
            <p>
                © {{ date('Y') }} LuckSpin. All rights reserved.
            </p>
        </div>
    </div>
</footer>
