@extends('admin.base')

@section('content')

<div class="container-fluid">


    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>
            Deposit Details
        </h4>


        <a href="{{ route('admin.deposits.index') }}"
           class="btn btn-secondary">

            Back

        </a>

    </div>




    <div class="row">


        {{-- Deposit Information --}}
        <div class="col-md-8">


            <div class="card">


                <div class="card-header">

                    <h5 class="mb-0">
                        Deposit Information
                    </h5>

                </div>



                <div class="card-body">


                    <table class="table table-bordered">


                        <tr>

                            <th width="200">
                                User
                            </th>

                            <td>

                                {{ $deposit->user->name ?? 'N/A' }}

                                <br>

                                <small>
                                    {{ $deposit->user->email ?? '' }}
                                </small>

                            </td>

                        </tr>



                        <tr>

                            <th>
                                Amount
                            </th>

                            <td>

                                <strong>
                                    {{ number_format($deposit->amount, 2) }}
                                    {{ $deposit->currency }}
                                </strong>

                            </td>

                        </tr>



                        <tr>

                            <th>
                                Status
                            </th>

                            <td>

                                <span class="badge bg-{{ $deposit->status_badge }}">

                                    {{ $deposit->status_label }}

                                </span>

                            </td>

                        </tr>



                        <tr>

                            <th>
                                Reference Number
                            </th>

                            <td>

                                {{ $deposit->reference_number ?? '-' }}

                            </td>

                        </tr>



                        <tr>

                            <th>
                                User Remarks
                            </th>

                            <td>

                                {{ $deposit->remarks ?? '-' }}

                            </td>

                        </tr>



                        <tr>

                            <th>
                                Submitted At
                            </th>

                            <td>

                                {{ $deposit->created_at->format('d M Y h:i A') }}

                            </td>

                        </tr>



                    </table>


                </div>


            </div>



            {{-- Payment Proof --}}
            <div class="card">


                <div class="card-header">

                    <h5 class="mb-0">
                        Payment Proof
                    </h5>

                </div>



                <div class="card-body text-center">


                    @if($deposit->payment_proof)

                        <a href="{{ asset('storage/'.$deposit->payment_proof) }}"
                           target="_blank">


                            <img src="{{ asset('storage/'.$deposit->payment_proof) }}"
                                 class="img-fluid rounded border"
                                 style="max-height:400px;">


                        </a>


                    @else

                        <p class="text-muted">
                            No payment proof uploaded.
                        </p>

                    @endif


                </div>


            </div>



        </div>





        {{-- Sidebar --}}
        <div class="col-md-4">


            {{-- Bank Details --}}
            <div class="card">


                <div class="card-header">

                    <h5 class="mb-0">
                        Bank Account
                    </h5>

                </div>



                <div class="card-body">


                    @if($deposit->bankAccount)

                        <p>
                            <strong>
                                Bank:
                            </strong>

                            {{ $deposit->bankAccount->bank_name }}
                        </p>



                        <p>
                            <strong>
                                Account Title:
                            </strong>

                            {{ $deposit->bankAccount->account_title }}
                        </p>



                        <p>
                            <strong>
                                Account Number:
                            </strong>

                            {{ $deposit->bankAccount->account_number }}
                        </p>



                        @if($deposit->bankAccount->iban)

                            <p>
                                <strong>
                                    IBAN:
                                </strong>

                                {{ $deposit->bankAccount->iban }}
                            </p>

                        @endif


                    @else

                        <p>
                            Bank account not found.
                        </p>

                    @endif


                </div>


            </div>





            {{-- Admin Action --}}
            @if($deposit->isPending())


            <div class="card">


                <div class="card-header">

                    <h5 class="mb-0">
                        Admin Action
                    </h5>

                </div>



                <div class="card-body">


                    {{-- Approve --}}
                    <form method="POST"
                          action="{{ route('admin.deposits.approve',$deposit) }}"
                          class="mb-3">


                        @csrf


                        <div class="mb-3">

                            <label>
                                Approval Remarks
                            </label>


                            <textarea name="admin_remarks"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Optional remarks"></textarea>

                        </div>



                        <button class="btn btn-success w-100">

                            Approve Deposit

                        </button>


                    </form>





                    {{-- Reject --}}
                    <form method="POST"
                          action="{{ route('admin.deposits.reject',$deposit) }}">


                        @csrf



                        <div class="mb-3">

                            <label>
                                Rejection Reason
                            </label>


                            <textarea name="admin_remarks"
                                      class="form-control"
                                      rows="3"
                                      required
                                      placeholder="Reason for rejection"></textarea>


                        </div>



                        <button class="btn btn-danger w-100">

                            Reject Deposit

                        </button>


                    </form>


                </div>


            </div>


            @endif





            {{-- Process History --}}
            @if(!$deposit->isPending())


            <div class="card">


                <div class="card-header">

                    <h5 class="mb-0">
                        Processing Details
                    </h5>

                </div>


                <div class="card-body">


                    <p>

                        <strong>
                            Processed By:
                        </strong>

                        {{ $deposit->approvedBy->name ?? '-' }}

                    </p>



                    <p>

                        <strong>
                            Processed At:
                        </strong>

                        {{ optional($deposit->approved_at)->format('d M Y h:i A') }}

                    </p>



                    <p>

                        <strong>
                            Admin Remarks:
                        </strong>

                        {{ $deposit->admin_remarks ?? '-' }}

                    </p>


                </div>


            </div>


            @endif



        </div>


    </div>


</div>


@endsection