@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h4>
                Create Bank Account
            </h4>
        </div>


        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.bank-accounts.store') }}"
                  enctype="multipart/form-data">

                @csrf

                @include('admin.bank_accounts.form')


                <button type="submit"
                        class="btn btn-primary">

                    Save Account

                </button>

                <a href="{{ route('admin.bank-accounts.index') }}"
                   class="btn btn-secondary">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

@endsection