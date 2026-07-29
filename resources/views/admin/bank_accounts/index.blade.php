@extends('admin.base')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                Bank Accounts
            </h4>
            <a href="{{ route('admin.bank-accounts.create') }}" class="btn btn-primary">
                Add Bank Account
            </a>
        </div>
        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bank-accounts.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search bank/account..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">
                                All Status
                            </option>

                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button class="btn btn-dark">
                            Filter
                        </button>

                    </div>


                </form>

            </div>

        </div>



        {{-- Table --}}
        <div class="card">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-striped mb-0">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Bank</th>
                                <th>Account</th>
                                <th>Currency</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th width="150">
                                    Action
                                </th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse($bankAccounts as $account)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>


                                    <td>
                                        {{ $account->title }}
                                    </td>


                                    <td>
                                        {{ $account->bank_name }}
                                    </td>


                                    <td>

                                        {{ $account->account_title }}

                                        <br>

                                        <small>
                                            {{ $account->masked_account_number }}
                                        </small>

                                    </td>


                                    <td>
                                        {{ $account->currency }}
                                    </td>


                                    <td>
                                        {{ ucfirst($account->type) }}
                                    </td>


                                    <td>

                                        <span class="badge bg-{{ $account->status_badge }}">

                                            {{ $account->status_label }}

                                        </span>

                                    </td>


                                    <td>


                                        <a href="{{ route('admin.bank-accounts.edit', $account) }}"
                                            class="btn btn-sm btn-warning">

                                            Edit

                                        </a>



                                        <form action="{{ route('admin.bank-accounts.destroy', $account) }}" method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')


                                            <button type="submit" onclick="return confirm('Delete this account?')"
                                                class="btn btn-sm btn-danger">

                                                Delete

                                            </button>

                                        </form>


                                    </td>


                                </tr>


                            @empty

                                <tr>

                                    <td colspan="8" class="text-center">

                                        No bank accounts found.

                                    </td>

                                </tr>

                            @endforelse


                        </tbody>

                    </table>

                </div>

            </div>


            <div class="card-footer">

                {{ $bankAccounts->links() }}

            </div>


        </div>


    </div>

@endsection