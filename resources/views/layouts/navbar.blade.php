<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars fa-2x"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ url('/home') }}" class="nav-link"><i class="fas fa-home text-teal mr-1"></i> หน้าแรก</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ url('/about') }}" class="nav-link"><i class="fas fa-layer-group text-teal mr-1"></i> เกี่ยวกับ</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ url('/contact') }}" class="nav-link"><i class="fas fa-envelope text-teal mr-1"></i> ติดต่อเรา</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Authentication Links -->
        @guest
            @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                </li>
            @endif
            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">สมัครสมาชิก</a>
                </li>
            @endif
        @else
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle text-teal mr-1"></i>
                    สวัสดี : {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user mr-2 text-teal"></i>
                        ข้อมูลส่วนตัว
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-cog mr-2 text-teal"></i>
                        ตั้งค่า
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-2 text-danger"></i>
                        ออกจากระบบ
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        @endguest
    </ul>

</nav>
