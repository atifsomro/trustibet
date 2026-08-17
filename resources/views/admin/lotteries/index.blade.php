@extends('admin.base')

@section('content')

<div class="container-fluid">

    {{-- ================================================================
         HEADER
    ================================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Lotteries
        </h4>

        <a
            href="{{ route('admin.lotteries.create') }}"
            class="btn btn-primary"
        >
            Add Lottery
        </a>

    </div>


    {{-- ================================================================
         FILTERS
    ================================================================= --}}

    <div class="card mb-3">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('admin.lotteries.index') }}"
                class="row g-3"
            >

                {{-- Search --}}
                <div class="col-md-5">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search lottery..."
                        value="{{ request('search') }}"
                    >

                </div>


                {{-- Status --}}
                <div class="col-md-3">

                    <select
                        name="status"
                        class="form-control"
                    >

                        <option value="">
                            All Statuses
                        </option>

                        @foreach([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'selling' => 'Selling',
                            'ended' => 'Ended',
                            'drawing' => 'Drawing',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ] as $key => $label)

                            <option
                                value="{{ $key }}"
                                {{ request('status') === $key ? 'selected' : '' }}
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Active --}}
                <div class="col-md-2">

                    <select
                        name="active"
                        class="form-control"
                    >

                        <option value="">
                            All
                        </option>

                        <option
                            value="1"
                            {{ request('active') === '1' ? 'selected' : '' }}
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            {{ request('active') === '0' ? 'selected' : '' }}
                        >
                            Inactive
                        </option>

                    </select>

                </div>


                {{-- Filter Buttons --}}
                <div class="col-md-2">

                    <button
                        type="submit"
                        class="btn btn-dark"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.lotteries.index') }}"
                        class="btn btn-light"
                    >
                        Reset
                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- ================================================================
         LOTTERY TABLE
    ================================================================= --}}

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle mb-0">

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Lottery
                            </th>

                            <th>
                                Ticket
                            </th>

                            <th>
                                Prize Pool
                            </th>

                            <th>
                                Draws
                            </th>

                            <th>
                                Sales Close
                            </th>

                            <th>
                                Draw
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Active
                            </th>

                            <th width="300">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($lotteries as $lottery)

                        <tr>

                            {{-- =================================================
                                 #
                            ================================================== --}}

                            <td>
                                {{ $lotteries->firstItem() + $loop->index }}
                            </td>


                            {{-- =================================================
                                 LOTTERY
                            ================================================== --}}

                            <td>

                                <strong>
                                    {{ $lottery->title }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    {{ $lottery->currency }}
                                </small>

                            </td>


                            {{-- =================================================
                                 TICKET PRICE
                            ================================================== --}}

                            <td>

                                {{ $lottery->currency }}

                                {{ number_format(
                                    (float) $lottery->ticket_price,
                                    2
                                ) }}

                            </td>


                            {{-- =================================================
                                 PRIZE POOL
                            ================================================== --}}

                            <td>

                                {{ $lottery->currency }}

                                {{ number_format(
                                    (float) $lottery->totalPrizeAmount(),
                                    2
                                ) }}

                            </td>


                            {{-- =================================================
                                 DRAWS
                            ================================================== --}}

                            <td>

                                <span class="badge bg-info">

                                    {{ $lottery->completed_draws_count }}

                                </span>

                                <small class="text-muted">

                                    /
                                    {{ $lottery->total_draws_count }}

                                </small>

                            </td>


                            {{-- =================================================
                                 SALES CLOSE
                            ================================================== --}}

                            <td>

                                {{ optional(
                                    $lottery->sales_end_at
                                )->format('d M Y h:i A') ?? '—' }}

                            </td>


                            {{-- =================================================
                                 DRAW DATE
                            ================================================== --}}

                            <td>

                                {{ optional(
                                    $lottery->draw_at
                                )->format('d M Y h:i A') ?? '—' }}

                            </td>


                            {{-- =================================================
                                 STATUS
                            ================================================== --}}

                            <td>

                                <span
                                    class="badge bg-{{
                                        $lottery->status_badge
                                    }}"
                                >

                                    {{ $lottery->status_label }}

                                </span>

                            </td>


                            {{-- =================================================
                                 ACTIVE
                            ================================================== --}}

                            <td>

                                <span
                                    class="badge bg-{{
                                        $lottery->is_active
                                            ? 'success'
                                            : 'secondary'
                                    }}"
                                >

                                    {{ $lottery->is_active
                                        ? 'Yes'
                                        : 'No'
                                    }}

                                </span>

                            </td>


                            {{-- =================================================
                                 ACTIONS
                            ================================================== --}}

                            <td>

                                {{-- Edit --}}
                                <a
                                    href="{{ route(
                                        'admin.lotteries.edit',
                                        $lottery
                                    ) }}"
                                    class="btn btn-sm btn-warning"
                                >
                                    Edit
                                </a>


                                {{-- Results --}}
                                @if ($lottery->completed_draws_count > 0)

                                    <a
                                        href="{{ route(
                                            'admin.lotteries.show',
                                            $lottery
                                        ) }}"
                                        class="btn btn-sm btn-success"
                                    >

                                        <i class="fas fa-trophy"></i>

                                        Results

                                    </a>

                                @endif


                                {{-- =================================================
                                     DRAW WINNERS
                                     ONLY WHEN STATUS IS ENDED
                                ================================================== --}}

                                @if ($lottery->isEnded())

                                    <form
                                        action="{{ route(
                                            'admin.lotteries.draw',
                                            $lottery
                                        ) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Are you sure you want to draw the winners? This action cannot be reversed.'
                                        )"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-primary"
                                        >

                                            <i class="fas fa-trophy"></i>

                                            Draw Winners

                                        </button>

                                    </form>

                                @endif


                                {{-- =================================================
                                     DELETE
                                ================================================== --}}

                                @if (
                                    !$lottery->tickets()->exists()
                                    &&
                                    !$lottery->draws()->exists()
                                )

                                    <form
                                        action="{{ route(
                                            'admin.lotteries.destroy',
                                            $lottery
                                        ) }}"
                                        method="POST"
                                        class="d-inline"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            onclick="return confirm(
                                                'Delete this lottery?'
                                            )"
                                            class="btn btn-sm btn-danger"
                                        >

                                            Delete

                                        </button>

                                    </form>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="text-center py-4"
                            >
                                No lotteries found.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ================================================================
             PAGINATION
        ================================================================= --}}

        <div class="card-footer">

            {{ $lotteries->links() }}

        </div>

    </div>

</div>

@endsection