@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Create Investment Package</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.investment-packages.store') }}">
                    @csrf
                    @include('admin.investment_packages.form')
                    <button type="submit" class="btn btn-success">Save Package</button>
                    <a href="{{ route('admin.investment-packages.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
