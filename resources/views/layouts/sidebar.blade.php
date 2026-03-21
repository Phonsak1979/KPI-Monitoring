<aside class="main-sidebar sidebar-light-teal elevation-4" id="main-sidebar">

    <!-- Logo -->
    <a href="#" class="brand-link bg-warning">
        <img src="{{ asset('dist/img/MOPH-Logo.png') }}" alt="MOPH-Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span style="font-family: 'Audiowide', sans-serif; font-size: 18px;"
            class="brand-text font-weight-bold">KPI-Monitoring</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- User panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <!-- User Image -->
            <div class="image">
                <img src="{{ asset('dist/img/Phonsak.jpg') }}" class="img-circle elevation-2" alt="User Image"
                    style="width: 45px; height: 45px; object-fit: cover; border: 1px solid #ced4da; padding: 1px; background-color: #fff;">
            </div>
            <!-- User Info -->
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()?->name }}</a>
                <span class="badge bg-lime">{{ Auth::user()?->role }}</span>
            </div>
        </div>

        <!-- Menu panel -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Navbar Header -->
                <li class="nav-header text-teal">Main Menu</li>

                <!-- Navbar Item -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-th-large"></i>
                        <p>
                            หน้าแรก
                        </p>
                    </a>
                </li>
                <!-- Navbar Item -->
                <li class="nav-item">
                    <a href="{{ route('report.index') }}" class="nav-link {{ request()->is('report*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            รายงาน
                        </p>
                    </a>
                </li>

                <!-- Navbar Header -->
                <li class="nav-header text-teal">Setting Menu</li>

                <!-- Navbar Item -->
                <li class="nav-item">
                    <a href="{{ route('districts.index') }}"
                        class="nav-link {{ request()->is('districts') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            อำเภอ
                        </p>
                    </a>
                </li>

                <!-- Navbar Item -->
                <li class="nav-item">
                    <a href="{{ route('hospitals.index') }}"
                        class="nav-link {{ request()->is('hospitals') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            หน่วยบริการ
                        </p>
                    </a>
                </li>

                <!-- Navbar Item -->
                <li class="nav-item">
                    <a href="{{ route('departments.index') }}"
                        class="nav-link {{ request()->is('departments') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            กลุ่มงาน/ฝ่าย
                        </p>
                    </a>
                </li>

                <!-- Navbar Item -->
                <li class="nav-item">
                    <a href="{{ route('rankings.index') }}"
                        class="nav-link {{ request()->is('rankings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            ตัวชี้วัด
                        </p>
                    </a>
                </li>


                <!-- Navbar Header -->
                <li class="nav-header text-teal">System Menu</li>
                @if (Auth::user()->role !== 'user')
                    <!-- Navbar Item -->
                    <li class="nav-item">
                        <a href="{{ route('sync.index') }}"
                            class="nav-link {{ request()->is('sync*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-sync-alt text-primary"></i>
                            <p class="text-primary">
                                MOPH Open-Data
                            </p>
                        </a>
                    </li>
                @endif

                <!-- Navbar Item 8 -->
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p class="text-danger">
                            ออกจากระบบ
                        </p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

            </ul>
        </nav>

    </div>

</aside>
