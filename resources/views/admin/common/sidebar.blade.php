@php
    $active = $active ?? 'home';
@endphp

<aside class="left-sidebar">

    <div class="scroll-sidebar">

        <nav class="sidebar-nav">

            <ul id="sidebarnav">

                {{-- User --}}
                <li class="user-pro">

                    <a class="has-arrow waves-effect waves-dark"
                       href="javascript:void(0)"
                       aria-expanded="false">

                        <img src="{{ asset('assets/admin_assets/images/users/1.jpg') }}"
                             alt="user-img"
                             class="img-circle">

                        <span class="hide-menu">

                            Super Admin

                        </span>

                    </a>

                    <ul aria-expanded="false"
                        class="collapse">

                        <li>

                            <a href="javascript:void(0)">

                                <i class="ti-settings"></i>

                                Account Setting

                            </a>

                        </li>

                        <li>

                            <a href="{{ route('admin.logout') }}">

                                <i class="fa fa-power-off"></i>

                                Logout

                            </a>

                        </li>

                    </ul>

                </li>

                {{-- Dashboard --}}
                <li class="{{ $active == 'dashboard' ? 'active' : '' }}">

                    <a href="{{ route('admin.dashboard') }}">

                        <i class="icon-speedometer"></i>

                        <span class="hide-menu">

                            Dashboard

                        </span>

                    </a>

                </li>

                {{-- Users --}}
                <li class="{{ $active == 'users' ? 'active' : '' }}">

                    <a href="{{ route('admin.users.index') }}">

                        <i class="ti-user"></i>

                        <span class="hide-menu">

                            Users

                        </span>

                    </a>

                </li>

                {{-- Bank Accounts --}}
                <li class="{{ $active == 'bank-accounts' ? 'active' : '' }}">

                    <a href="{{ route('admin.bank-accounts.index') }}">

                        <i class="ti-credit-card"></i>

                        <span class="hide-menu">

                            Bank Accounts

                        </span>

                    </a>

                </li>

                {{-- Deposits --}}
                <li class="{{ $active == 'deposits' ? 'active' : '' }}">

                    <a href="{{ route('admin.deposits.index') }}">

                        <i class="ti-money"></i>

                        <span class="hide-menu">

                            Deposits

                            @php
                                $pendingDeposits = \App\Models\Deposit::where('status', 'pending')->count();
                            @endphp

                            @if($pendingDeposits)

                                <span class="badge badge-pill badge-warning ml-2">

                                    {{ $pendingDeposits }}

                                </span>

                            @endif

                        </span>

                    </a>

                </li>

                {{-- Wallets --}}
                <li class="{{ $active == 'wallets' ? 'active' : '' }}">

                    <a href="{{ route('admin.wallets.index') }}">

                        <i class="ti-wallet"></i>

                        <span class="hide-menu">

                            Wallets

                        </span>

                    </a>

                </li>

                {{-- Withdrawals --}}
                <li class="{{ $active == 'withdrawals' ? 'active' : '' }}">

                    <a href="{{ route('admin.withdrawals.index') }}">

                        <i class="ti-share"></i>

                        <span class="hide-menu">

                            Withdrawals
                            
                            @php
                                $pendingWithdrawals = \App\Models\WithdrawalRequest::where('status', \App\Enums\WithdrawalStatus::PENDING)->count();
                            @endphp

                            @if($pendingWithdrawals)

                                <span class="badge badge-pill badge-danger ml-2">

                                    {{ $pendingWithdrawals }}

                                </span>

                            @endif

                        </span>

                    </a>

                </li>

            </ul>

        </nav>

    </div>

</aside>