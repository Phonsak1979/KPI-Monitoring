<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>

<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

<!-- Sparkline -->
<script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>

<!-- JQVMap -->
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>

<!-- jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>

<!-- daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>

<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<!-- SweetAlert2 & Toast Global Configuration -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toast Configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if (Session::has('success'))
        Toast.fire({
            icon: 'success',
            title: '{{ Session::get('success') }}'
        });
    @endif

    // Global Delete Confirmation
    $(document).on('click', '.delete-confirm', function(event) {
        var form = $(this).closest("form");
        event.preventDefault();
        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: "คุณต้องการลบข้อมูลนี้ ใช่หรือไม่!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> ลบข้อมูล',
            cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>

<!-- Dark Mode Toggle Script -->
<script>
    $(document).ready(function() {
        const body = document.getElementById('body-tag') || document.body;
        const sidebar = document.getElementById('main-sidebar');
        const navbar = document.getElementById('main-navbar');
        const toggleBtn = document.getElementById('dark-mode-toggle');
        const icon = document.getElementById('dark-mode-icon');
        const isDark = localStorage.getItem('dark-mode') === 'true';

        // Initialize on page load
        function applyDarkMode(enabled) {
            if (enabled) {
                body.classList.add('dark-mode');
                if (sidebar) {
                    sidebar.classList.remove('sidebar-light-teal');
                    sidebar.classList.add('sidebar-dark-teal');
                }
                if (navbar) {
                    navbar.classList.remove('navbar-white', 'navbar-light');
                    navbar.classList.add('navbar-dark');
                }
                if (icon) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                    icon.style.color = '#ffc107';
                }
            } else {
                body.classList.remove('dark-mode');
                if (sidebar) {
                    sidebar.classList.remove('sidebar-dark-teal');
                    sidebar.classList.add('sidebar-light-teal');
                }
                if (navbar) {
                    navbar.classList.remove('navbar-dark');
                    navbar.classList.add('navbar-white', 'navbar-light');
                }
                if (icon) {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                    icon.style.color = '';
                }
            }
        }

        // Apply saved preference
        applyDarkMode(isDark);

        // Toggle handler
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const currentlyDark = body.classList.contains('dark-mode');
                const newState = !currentlyDark;
                localStorage.setItem('dark-mode', newState);
                applyDarkMode(newState);
            });
        }
    });
</script>

@yield('JS')
