@extends('admin.base')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Create Lottery</h4>
        <a href="{{ route('admin.lotteries.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.lotteries.store') }}">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                @include('admin.lotteries.form')

                <div class="mb-4">
                    <button type="submit" class="btn btn-primary mr-2">Save Lottery</button>
                    <a href="{{ route('admin.lotteries.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.lotteries.partials.form-sidebar')
            </div>
        </div>
    </form>
</div>
@endsection
