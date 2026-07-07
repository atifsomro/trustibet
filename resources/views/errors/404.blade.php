@extends('layouts.master')

@section('title', '404 - Page Not Found')

@section('content')
    <section class="min-h-[80vh] flex items-center py-16 overflow-hidden">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center">
                <div class="relative inline-flex items-center justify-center mb-10">
                    <div class="absolute w-72 h-72 rounded-full bg-brand-primary/10 blur-3xl"></div>
                    <div
                        class="relative w-40 h-40 rounded-full border border-brand-border bg-brand-surface flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-6xl text-brand-primary"></i>
                    </div>
                </div>
                <h1 class="text-[90px] md:text-[150px] lg:text-[200px] font-black leading-none text-brand-primary">
                    404
                </h1>
                <h2 class="mt-6">
                    Oops! Page Not Found
                </h2>
                <p class="max-w-2xl mx-auto mt-5 opacity-70 leading-8">
                    The page you are trying to access doesn't exist, may have been moved or the URL may be incorrect. Please
                    check the address or use one of the options below.
                </p>
                <div class="flex flex-wrap justify-center gap-4 mt-10">
                    <a href="{{ route('home') }}" class="btn-primary">
                        <i class="fa-solid fa-house mr-2"></i>
                        Go To Homepage
                    </a>
                    <button onclick="history.back()" class="btn-secondary">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Go Back
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection
