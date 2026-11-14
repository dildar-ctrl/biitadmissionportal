@extends('layouts.auth')

@section('title', 'Sign In - BiiT University')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <img src="{{ asset('logo.png') }}" alt="BIIT Logo" class="center" />
        <h4 style="text-align: center; margin-top: 10px; color: #0c7347; font-size: 20px">
            Welcome to <br />
            Barani Institute <br />
            Admission Portal
        </h4>
        <div class="input_field" style="margin-top: 50px">
            <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}" required autofocus />
            <input type="password" name="password" id="password" placeholder="Password" required />
        </div>
        <p class="psw" style="text-align: right; margin-top: -10px; margin-bottom: 15px;">
    <a href="{{ route('password.forgot') }}">Forgot Password?</a>
</p>
        <button type="submit" id="submitBtn" style="margin-top: 20px">
            <i class="fas fa-sign-in-alt me-2"></i>SIGN IN
        </button>
        <p class="mt-2">
            Don't have an account? <a href="{{ route('signup') }}" class="register">   SignUp here</a>
        </p>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $('form').on('submit', function(e) {
        const email = $('#email').val();
        const password = $('#password').val();

        if (!email || !password) {
            e.preventDefault();
            Swal.fire({
                title: 'Validation Error',
                text: 'Please enter both email and password.',
                icon: 'error',
                confirmButtonColor: '#0c7347'
            });
            return false;
        }

        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Signing in...');
        $('#preloader').fadeIn(300);
        return true;
    });
</script>
@endpush