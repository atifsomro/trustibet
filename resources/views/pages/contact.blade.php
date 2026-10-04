@extends('layouts.master')

@section('content')

    <section class="contact-page py-16 lg:py-20">
        <div class="container">

            <div class="contact-page__wrapper max-w-4xl mx-auto">

                {{-- Heading --}}
                <div class="contact-page__heading text-center">

                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Contact Us
                    </small>

                    <h1 class="mt-4">
                        We'd Love To
                        <span class="text-brand-light text-inherit">
                            Hear From You
                        </span>
                    </h1>

                    <p class="mt-5">
                        Whether you have questions about your account, payments,
                        withdrawals, or games, our support team is always here
                        to help. Fill out the form below and we'll get back to
                        you as quickly as possible.
                    </p>

                </div>

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="mt-6 rounded-xl border border-green-500/30 bg-green-500/10 px-5 py-4 text-green-400">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Guidance --}}
                <div class="contact-page__guide mt-10 rounded-2xl border border-brand-border bg-brand-surface p-6">

                    <div class="grid gap-4 md:grid-cols-3">

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clock text-brand-light text-xl"></i>

                            <div>
                                <h5>Quick Response</h5>
                                <small>Usually within 24 hours</small>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-shield-halved text-brand-light text-xl"></i>

                            <div>
                                <h5>Private & Secure</h5>
                                <small>Your information is protected</small>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-headset text-brand-light text-xl"></i>

                            <div>
                                <h5>24/7 Support</h5>
                                <small>Always ready to assist</small>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- Form --}}
                <form
                    action="{{ route('contact.send') }}"
                    method="POST"
                    class="mt-10 rounded-3xl border border-brand-border bg-brand-surface p-8 lg:p-10"
                >

                    @csrf

                    {{-- Full Name + Email --}}
                    <div class="grid gap-6 md:grid-cols-2">

                        {{-- Full Name --}}
                        <div>

                            <label for="full_name" class="mb-2 block">
                                Full Name
                            </label>

                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="{{ old('full_name') }}"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                placeholder="Enter your full name"
                                maxlength="100"
                                required
                            >

                            @error('full_name')
                                <small class="mt-1 block text-red-500">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                        {{-- Email --}}
                        <div>

                            <label for="email" class="mb-2 block">
                                Email Address
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                placeholder="Enter your email"
                                maxlength="255"
                                required
                            >

                            @error('email')
                                <small class="mt-1 block text-red-500">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                    </div>

                    {{-- Subject --}}
                    <div class="mt-6">

                        <label for="subject" class="mb-2 block">
                            Subject
                        </label>

                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            value="{{ old('subject') }}"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="Enter subject"
                            maxlength="150"
                            required
                        >

                        @error('subject')
                            <small class="mt-1 block text-red-500">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>

                    {{-- Department --}}
                    <div class="mt-6">

                        <label for="department" class="mb-2 block">
                            Department
                        </label>

                        <select
                            id="department"
                            name="department"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            required
                        >
                            <option value="">Select Department</option>

                            <option value="General Inquiry"
                                @selected(old('department') === 'General Inquiry')>
                                General Inquiry
                            </option>

                            <option value="Account Support"
                                @selected(old('department') === 'Account Support')>
                                Account Support
                            </option>

                            <option value="Payments"
                                @selected(old('department') === 'Payments')>
                                Payments
                            </option>

                            <option value="Technical Issue"
                                @selected(old('department') === 'Technical Issue')>
                                Technical Issue
                            </option>
                        </select>

                        @error('department')
                            <small class="mt-1 block text-red-500">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>

                    {{-- Message --}}
                    <div class="mt-6">

                        <label for="message" class="mb-2 block">
                            Message
                        </label>

                        <textarea
                            rows="7"
                            id="message"
                            name="message"
                            class="w-full resize-none rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="Write your message here..."
                            minlength="10"
                            maxlength="5000"
                            required
                        >{{ old('message') }}</textarea>

                        @error('message')
                            <small class="mt-1 block text-red-500">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="btn-primary mt-8"
                    >
                        Send Message
                    </button>

                </form>

            </div>

        </div>
    </section>

@endsection
