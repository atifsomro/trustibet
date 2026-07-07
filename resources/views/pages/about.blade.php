@extends('layouts.master')

@section('content')
    <section class="about py-16 lg:py-20">
        <div class="container">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20">
                {{-- Left --}}
                <div class="about__image relative">
                    <img src="{{ asset('/images/about/about.png') }}" alt="About Us"
                        class="w-full h-[550px] rounded-3xl border border-brand-border object-cover">
                    <div class="absolute bottom-80 left-6 bg-brand-surface border border-brand-border rounded-2xl px-6 py-5">
                        <h3 class="text-brand-primary">10K+</h3>
                        <p class="mt-1">Happy Players Worldwide</p>
                    </div>
                </div>

                {{-- Right --}}
                <div class="about__content">
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        About Us
                    </small>
                    <h2 class="mt-3">
                        The Ultimate
                        <span class="text-brand-primary text-inherit">
                            Online Gaming Experience
                        </span>
                    </h2>
                    <p class="mt-6">
                        We are passionate about delivering a premium online gaming experience
                        where entertainment, fairness, and security come together. Our platform
                        offers a wide collection of casino games designed for players of every
                        skill level.
                    </p>
                    <p class="mt-5">
                        Whether you enjoy classic casino games or exciting new releases, our
                        goal is to provide a smooth, secure, and rewarding experience every
                        time you play.
                    </p>
                    <div class="mt-8 space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-brand-primary/15 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-shield-halved text-brand-primary"></i>
                            </div>
                            <div>
                                <h4>
                                    Safe & Secure Platform
                                </h4>
                                <p class="mt-2">
                                    Advanced security systems keep your account and transactions protected.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-brand-primary/15 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-headset text-brand-primary"></i>
                            </div>
                            <div>
                                <h4>
                                    Dedicated Support
                                </h4>
                                <p class="mt-2">
                                    Our friendly support team is available whenever you need assistance.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 flex flex-wrap gap-4">
                        <a href="{{ route('contact') }}" class="btn-secondary">
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
