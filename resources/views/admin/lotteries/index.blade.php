@extends('admin.base')

@section('content')

<div class="container-fluid">

    {{-- ================================================================
        Header
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
        Filters
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


                {{-- Filter --}}
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
        Lottery Table
    ================================================================= --}}
    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped align-middle mb-0">

                    <thead>

                        <tr>

                            <th>#</th>

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

                            {{-- ====================================================
                                #
                            ===================================================== --}}
                            <td>
                                {{ $lotteries->firstItem() + $loop->index }}
                            </td>


                            {{-- ====================================================
                                Lottery
                            ===================================================== --}}
                            <td>

                                <strong>
                                    {{ $lottery->title }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    {{ $lottery->currency }}
                                </small>

                            </td>


                            {{-- ====================================================
                                Ticket Price
                            ===================================================== --}}
                            <td>

                                {{ $lottery->currency }}
                                {{ number_format(
                                    (float) $lottery->ticket_price,
                                    2
                                ) }}

                            </td>


                            {{-- ====================================================
                                Prize Pool
                            ===================================================== --}}
                            <td>

                                {{ $lottery->currency }}
                                {{ number_format(
                                    (float) $lottery->total_prize_pool,
                                    2
                                ) }}

                            </td>


                            {{-- ====================================================
                                Completed Draws
                            ===================================================== --}}
                            <td>

                                @if ($lottery->completed_draws_count > 0)

                                    <a
                                        href="{{ route(
                                            'admin.lotteries.show',
                                            $lottery
                                        ) }}"
                                        class="text-decoration-none"
                                    >

                                        <span class="badge bg-success">
                                            {{ $lottery->completed_draws_count }}
                                        </span>

                                        <small class="text-muted">
                                            {{ $lottery->completed_draws_count == 1
                                                ? 'Draw'
                                                : 'Draws'
                                            }}
                                        </small>

                                    </a>

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                Sales Close
                            ===================================================== --}}
                            <td>

                                {{ optional($lottery->sales_end_at)
                                    ->format('d M Y h:i A') }}

                            </td>


                            {{-- ====================================================
                                Draw Date
                            ===================================================== --}}
                            <td>

                                {{ optional($lottery->draw_at)
                                    ->format('d M Y h:i A') ?? '—' }}

                            </td>


                            {{-- ====================================================
                                Status
                            ===================================================== --}}
                            <td>

                                <span
                                    class="badge bg-{{ $lottery->status_badge }}"
                                >
                                    {{ $lottery->status_label }}
                                </span>

                            </td>


                            {{-- ====================================================
                                Active
                            ===================================================== --}}
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


                            {{-- ====================================================
                                Actions
                            ===================================================== --}}
                            <td>

                                {{-- =================================================
                                    Edit
                                ================================================== --}}
                                <a
                                    href="{{ route(
                                        'admin.lotteries.edit',
                                        $lottery
                                    ) }}"
                                    class="btn btn-sm btn-warning"
                                >
                                    Edit
                                </a>


                                {{-- =================================================
                                    Results
                                    Show when this lottery has at least one
                                    completed draw.
                                ================================================== --}}
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
                                    Draw Winners

                                    IMPORTANT:
                                    Only show this button when the CURRENT
                                    lottery round is in "ended" status.
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
                                    Delete

                                    Only allow delete if no tickets exist.
                                ================================================== --}}
                                @if (!$lottery->tickets()->exists())

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
            Pagination
        ================================================================= --}}
        <div class="card-footer">

            {{ $lotteries->links() }}

        </div>

    </div>

</div>

@endsection