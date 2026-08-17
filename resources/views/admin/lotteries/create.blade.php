@extends('admin.base')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4 class="mb-0">Create Lottery</h4></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.lotteries.store') }}">
                @csrf
                @include('admin.lotteries.form')
                <button type="submit" class="btn btn-primary">Save Lottery</button>
                <a href="{{ route('admin.lotteries.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
