<!DOCTYPE html>
<html lang="en">

<!-- Title -->
@section('title', 'ลงทะเบียน')

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
                        class="fas fa-user-plus mr-2"></i> Register System
                </h4>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Fullname -->
                    <label for="name">ชื่อ-นามสกุล</label>
                    <div class="input-group mb-3">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            placeholder="เช่น นายสมชาย ใจดี">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user text-success"></span>
                            </div>
                        </div>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <label for="email">Email</label>
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email"
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
                        <input id="password" type="password" class="form-control" name="password" required
                            placeholder="รหัสผ่าน 8 หลัก">
                        <div class="input-group-append">
                            <div class="input-group-text" style="cursor: pointer;" onclick="togglePassword1()">
                                <span class="fas fa-eye text-success" id="togglePasswordIcon1"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Retype Password -->
                    <label for="password-confirm">ยืนยันรหัสผ่าน</label>
                    <div class="input-group mb-3">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            required placeholder="ยืนยันรหัสผ่าน">
                        <div class="input-group-append">
                            <div class="input-group-text" style="cursor: pointer;" onclick="togglePassword2()">
                                <span class="fas fa-eye text-success" id="togglePasswordIcon2"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="far fa-check-square mr-2"></i>ลงทะเบียน</button>
                        </div>
                    </div>

                </form>

                <p class="mt-3 mb-0 text-sm">
                    หากเคยลงทะเบียนแล้ว <a href="{{ route('login') }}" class="text-center">คลิกที่นี่ !</a>
                </p>

            </div>
        </div>

    </div>

    <!-- Script -->
    @include('layouts.script')

    <script>
        function togglePassword1() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon1'); // ใช้ ID 1

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function togglePassword2() {
            const passwordInput = document.getElementById('password-confirm'); // เปลี่ยนเป็น password-confirm
            const toggleIcon = document.getElementById('togglePasswordIcon2'); // ใช้ ID 2

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>

</html>
