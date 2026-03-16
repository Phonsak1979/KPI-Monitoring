<!DOCTYPE html>
<html lang="en">

<!-- Title -->
@section('title', 'เข้าสู่ระบบ')

<!-- Head -->
@include('layouts.head')

@section('CSS')
    <link rel="preload" as="image" href="{{ asset('dist/img/background.jpg') }}">
@endsection

<style>
    body {
        /* ใส่พาธรูปภาพ */
        background-image: url('dist/img/background.jpg');
        background-color: #cccccc;
        /* สีพื้นหลังระหว่างรอโหลดภาพ */

        /* ให้ภาพพื้นหลังแสดงผลกลางหน้าจอ */
        background-repeat: no-repeat;
        background-attachment: fixed;

        /* ปรับภาพให้ขยายเต็มหน้าจอ */
        background-size: cover;
        background-position: center;
    }
</style>

<!-- Body -->

<body class="hold-transition login-page">
    <div class="login-box">

        <!-- Logo -->
        <div class="login-logo">
            <img src="dist/img/MOPH-Logo.png" alt="MOPH Logo" width="55" class="img-circle elevation-3">
            <a style="font-family: 'Audiowide', sans-serif; font-size: 33px;" href="#"
                class="text-warning">KPI-Monitoring</a>
        </div>

        <!-- Card -->
        <div class="card card-outline card-success" style="border-radius: 15px; overflow: hidden;">

            <!-- Card Body -->
            <div class="card-body login-card-body">
                <h4 style="font-family: 'Audiowide', sans-serif;" class="login-box-msg text-success"><i
                        class="fas fa-user-lock mr-2"></i> Login System
                </h4>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <label for="email">ชื่อผู้ใช้ (Email)</label>
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="example@example.com">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope text-success"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <label for="password">รหัสผ่าน</label>
                    <div class="input-group mb-3">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password" required
                            autocomplete="current-password" placeholder="รหัสผ่าน 8 หลัก">
                        <div class="input-group-append">

                            <div class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                <span class="fas fa-eye text-success" id="togglePasswordIcon"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Button Submit -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="nav-icon fas fa-sign-in-alt"></i> เข้าสู่ระบบ</button>
                        </div>
                    </div>

                </form>

                @if (Route::has('password.request'))
                    {{-- <p class="mt-3 mb-1 text-sm">
                        <a href="{{ route('password.request') }}">ลืมรหัสผ่านหรือไม่ ?</a>
                    </p> --}}
                    <p class="mt-3 mb-0 text-sm">
                        ต้องการลงทะเบียนเข้าใช้งาน <a href="{{ route('register') }}" class="text-center">คลิกที่นี่
                            !</a>
                    </p>
                @endif

            </div>
        </div>

    </div>

    <!-- Script -->
    @include('layouts.script')

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Popup แจ้งเตือน user ถูกระงับ
        @if ($errors->has('inactive'))
            Swal.fire({
                icon: 'warning',
                title: 'บัญชียังไม่ได้รับสิทธิ์เข้าใช้งาน',
                html: '<span style="font-size: 18px;">กรุณาติดต่อ <b class="text-primary">ผู้ดูแลระบบ</b> เพื่อดำเนินการอนุมัติ</span>',
                confirmButtonColor: '#28a745',
                confirmButtonText: '<i class="fas fa-sign-out-alt"></i> ออกจากระบบ'
            });
        @endif
    </script>
</body>

</html>
