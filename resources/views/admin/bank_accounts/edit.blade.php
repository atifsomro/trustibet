@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h4>
                Edit Bank Account
            </h4>
        </div>


        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.bank-accounts.update', $bankAccount) }}"
                  enctype="multipart/form-data">

                @csrf

                @method('PUT')


                @include('admin.bank_accounts.form')


                <button type="submit"
                        class="btn btn-primary">

                    Update Account

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