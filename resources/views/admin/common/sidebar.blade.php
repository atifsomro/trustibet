<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="user-pro"> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)"
                        aria-expanded="false"><img src="{{ asset('assets/admin_assets/images/users/1.jpg') }}"
                            alt="user-img" class="img-circle"><span class="hide-menu">Mark Jeckson</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="javascript:void(0)"><i class="ti-settings"></i> Account Setting</a></li>
                        <li><a href="{{ route('admin.logout') }}"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                </li>
                <li><a href="javascript:void(0)"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard</span></a></li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>