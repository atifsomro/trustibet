@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Edit Page</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pages.update', $page) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.pages.form')
                    <button type="submit" class="btn btn-success">Update Page</button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
