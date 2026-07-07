<section class="about py-8">
    <div class="container">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 lg:p-12">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left --}}
                <div>
                    <h2>
                        About
                        <span class="text-brand-primary text-inherit">
                            TrustiBet
                        </span>
                    </h2>
                    <p class="mt-6">
                        TrustiBet is your ultimate online gaming destination where
                        excitement meets trust. We offer a wide variety of casino
                        games, secure transactions, fast payouts and a fair play
                        environment for players worldwide.
                    </p>
                    <a href="{{ route('about') }}" class="btn-secondary mt-8 inline-flex items-center gap-3">
                        Learn More About Us
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                {{-- Right --}}
                <div class="grid sm:grid-cols-2 gap-8">
                    {{-- Item --}}
                    <div class="flex gap-5">
                        <div
                            class="w-18 h-18 rounded-full border border-blue-500/30 bg-blue-500/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shield-halved text-3xl text-blue-400"></i>
                        </div>
                        <div>
                            <h4>
                                Secure & Safe
                            </h4>
                            <p class="mt-2">
                                Advanced security to protect your account and data.
                            </p>
                        </div>
                    </div>
                    {{-- Item --}}
                    <div class="flex gap-5">
                        <div
                            class="w-18 h-18 rounded-full border border-fuchsia-500/30 bg-fuchsia-500/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-gamepad text-3xl text-fuchsia-400"></i>
                        </div>
                        <div>
                            <h4>
                                Exciting Games
                            </h4>
                            <p class="mt-2">
                                Hundreds of games including Slots, Dice and Wheels.
                            </p>
                        </div>
                    </div>
                    {{-- Item --}}
                    <div class="flex gap-5">
                        <div
                            class="w-18 h-18 rounded-full border border-yellow-500/30 bg-yellow-500/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-bolt text-3xl text-yellow-400"></i>
                        </div>
                        <div>
                            <h4>
                                Fast Payouts
                            </h4>
                            <p class="mt-2">
                                Quick and reliable withdrawals whenever you win.
                            </p>
                        </div>
                    </div>
                    {{-- Item --}}
                    <div class="flex gap-5">
                        <div
                            class="w-18 h-18 rounded-full border border-green-500/30 bg-green-500/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-headset text-3xl text-green-400"></i>
                        </div>
                        <div>
                            <h4>
                                24/7 Support
                            </h4>
                            <p class="mt-2">
                                Friendly customer support available anytime.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
