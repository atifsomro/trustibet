@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Create Page</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pages.store') }}">
                    @csrf
                    @include('admin.pages.form')
                    <button type="submit" class="btn btn-success">Save Page</button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
