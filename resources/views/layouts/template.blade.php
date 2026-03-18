<!DOCTYPE html>
<html lang="en">

<!-- Header -->
@include('layouts.head')

<body class="hold-transition sidebar-mini layout-fixed" id="body-tag">
<script>
    // Apply dark mode to body before render
    if (localStorage.getItem('dark-mode') === 'true') {
        document.getElementById('body-tag').classList.add('dark-mode');
    }
</script>

    <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Content -->
        <div class="content-wrapper">

            @yield('content')
            
        </div>

        <!-- Footer -->
        @include('layouts.footer')

    </div>

    @include('layouts.script')

</body>

</html>
