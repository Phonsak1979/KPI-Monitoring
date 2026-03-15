<!DOCTYPE html>
<html lang="en">

<!-- Title -->
@section('title', 'ลงทะเบียนสมาชิกใหม่')

<!-- Head -->
@include('layouts.head')

<style>
        body {
            /* ใส่พาธรูปภาพ */
            background-image: url('dist/img/background.jpg');
            
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
            <img src="dist/img/MOPH-Logo.png" alt="AdminLTE Logo" width="55" class="img-circle elevation-3">
            <a style="font-family: 'Audiowide', sans-serif; font-size: 30px;" href="#" class="text-warning">DMD-APPROVAL</a>
        </div>

        <!-- Card -->
        <div class="card card-outline card-success">

            <!-- Card Body -->
            <div class="card-body login-card-body">
                <h5 class="login-box-msg"><i class="fas fa-user-plus mr-2"></i></i>ลงทะเบียนสมาชิกใหม่</h5>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Fullname -->
                    <div class="input-group mb-3">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            placeholder="Full name">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user text-info"></span>
                            </div>
                        </div>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email"
                            placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope text-info"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control
                            @error('password') is-invalid @enderror" name="password" required
                            autocomplete="new-password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock text-info"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Retype Password -->
                    <div class="input-group mb-3">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Retype password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock text-info"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info btn-block">
                                <i class="far fa-check-square mr-2"></i>ลงทะเบียน</button>
                        </div>
                    </div>

                </form>

                <p class="mt-3 mb-1">
                    หากท่านเคยลงทะเบียนแล้ว <a href="{{ route('login') }}" class="text-center">คลิกที่นี่ !</a>
                </p>

            </div>
        </div>

    </div>

    <!-- Script -->
    @include('layouts.script')

</body>

</html>
