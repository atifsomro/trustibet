@extends('admin.base')

@section('content')

<div class="container-fluid">


    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Deposits
        </h4>

    </div>



    {{-- Filters --}}
    <div class="card mb-3">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.deposits.index') }}"
                  class="row g-3">


                <div class="col-md-5">

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search user or reference..."
                           value="{{ request('search') }}">

                </div>



                <div class="col-md-3">

                    <select name="status"
                            class="form-control">

                        <option value="">
                            All Status
                        </option>

                        <option value="pending"
                            {{ request('status') == 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>

                        <option value="approved"
                            {{ request('status') == 'approved' ? 'selected' : '' }}>
                            Approved
                        </option>

                        <option value="rejected"
                            {{ request('status') == 'rejected' ? 'selected' : '' }}>
                            Rejected
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




    {{-- Deposits Table --}}
    <div class="card">


        <div class="card-body p-0">


            <div class="table-responsive">


                <table class="table table-striped mb-0">


                    <thead>

                        <tr>

                            <th>#</th>

                            <th>User</th>

                            <th>Bank Account</th>

                            <th>Amount</th>

                            <th>Reference</th>

                            <th>Proof</th>

                            <th>Status</th>

                            <th width="100">
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                    @forelse($deposits as $deposit)


                        <tr>


                            <td>
                                {{ $loop->iteration }}
                            </td>



                            <td>

                                {{ $deposit->user->name ?? 'N/A' }}

                                <br>

                                <small>
                                    {{ $deposit->user->email ?? '' }}
                                </small>

                            </td>




                            <td>

                                {{ $deposit->bankAccount->bank_name ?? '' }}

                                <br>

                                <small>
                                    {{ $deposit->bankAccount->account_number ?? '' }}
                                </small>

                            </td>




                            <td>

                                {{ number_format($deposit->amount, 2) }}

                                {{ $deposit->currency }}

                            </td>




                            <td>

                                {{ $deposit->reference_number ?? '-' }}

                            </td>




                            <td>


                                @if($deposit->payment_proof)

                                    <a href="{{ asset('storage/'.$deposit->payment_proof) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-info">

                                        View

                                    </a>

                                @else

                                    -

                                @endif


                            </td>




                            <td>

                                <span class="badge bg-{{ $deposit->status_badge }}">

                                    {{ $deposit->status_label }}

                                </span>


                            </td>




                            <td>


                                <a href="{{ route('admin.deposits.show',$deposit) }}"
                                   class="btn btn-sm btn-primary">

                                    View

                                </a>


                            </td>



                        </tr>


                    @empty


                        <tr>

                            <td colspan="8"
                                class="text-center">

                                No deposits found.

                            </td>

                        </tr>


                    @endforelse



                    </tbody>


                </table>


            </div>


        </div>



        <div class="card-footer">

            {{ $deposits->links() }}

        </div>


    </div>


</div>


@endsection