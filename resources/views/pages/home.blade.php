@extends('layouts.master')

@section('title', 'Home')

@section('content')

    @include('sections.hero')
    @include('sections.featured-games')
    @include('sections.one-rupee-game')
    @include('sections.why-choose')
    @include('sections.promotions')
    @include('sections.ready-to-win')
    @include('sections.contact')

@endsection
