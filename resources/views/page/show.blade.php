@extends('layouts.master')

@section('title', $page->meta_title ?: $page->title)

@section('content')
    <section class="py-16">
        <div class="container">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-12">
                    <h1 class="mt-5">
                        {{ $page->title }}
                    </h1>
                </div>

                <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 mb-6">
                    <div class="opacity-80 leading-8">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
