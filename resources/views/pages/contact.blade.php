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
                <form class="mt-10 rounded-3xl border border-brand-border bg-brand-surface p-8 lg:p-10">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="fname" class="mb-2 block">
                                Full Name
                            </label>
                            <input type="text" id="fname"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                placeholder="Enter your full name">
                        </div>
                        <div>
                            <label for="email" class="mb-2 block">
                                Email Address
                            </label>
                            <input type="email" id="email"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                placeholder="Enter your email">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="uid" class="mb-2 block">
                            User ID
                        </label>
                        <input type="number" id="uid"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="Enter your ID">
                    </div>
                    <div class="mt-6">
                        <label for="subject" class="mb-2 block">
                            Subject
                        </label>
                        <input type="text" id="subject"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="Enter subject">
                    </div>
                    <div class="mt-6">
                        <label for="depart" class="mb-2 block">
                            Department
                        </label>
                        <select id="depart"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                            <option>General Inquiry</option>
                            <option>Account Support</option>
                            <option>Payments</option>
                            <option>Technical Issue</option>
                        </select>
                    </div>
                    <div class="mt-6">
                        <label for="message" class="mb-2 block">
                            Message
                        </label>
                        <textarea rows="7" id="message"
                            class="w-full resize-none rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="Write your message here..."></textarea>
                    </div>
                    <button class="btn-primary mt-8">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
