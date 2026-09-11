@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Edit Investment Package</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.investment-packages.update', $package) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.investment_packages.form')
                    <button type="submit" class="btn btn-success">Update Package</button>
                    <a href="{{ route('admin.investment-packages.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
