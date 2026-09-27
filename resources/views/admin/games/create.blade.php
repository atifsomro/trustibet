@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Create Game</h4>
            </div>
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.games.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.games.form', ['game' => new \App\Models\Game()])
                    <button type="submit" class="btn btn-success">Save Game</button>
                    <a href="{{ route('admin.games.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
