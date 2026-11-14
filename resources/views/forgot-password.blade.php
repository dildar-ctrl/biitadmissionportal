@extends('layouts.auth')

@section('title', 'Forgot Password - BiiT University')

@section('content')
<div class="container">
    <div style="width: 100%;">
        <h4 style="text-align: center; margin-top: 10px; color: #0c7347; font-size: 20px">
            Reset Your Password
        </h4>

        {{-- STEP 1: email + CNIC --}}
        <form id="verifyForm" style="margin-top: 40px;">
            @csrf
            <div class="input_field">
                <input type="email" name="email" id="verify_email" placeholder="Email" required autofocus />
                <input type="text" name="cnic" id="verify_cnic" placeholder="CNIC (xxxxx-xxxxxxx-x)" required />
            </div>
            <button type="submit" id="verifyBtn">
                <i class="fas fa-paper-plane me-2"></i>Send Verification Code
            </button>
            <p><a href="{{ route('login') }}">Back to Sign In</a></p>
        </form>

        {{-- STEP 2: enter OTP (hidden until code is sent) --}}
        <form id="otpForm" style="margin-top: 40px; display: none;">
            @csrf
            <p style="text-align:center; font-size: 14px; margin-bottom: 14px;">
                Enter the 6-digit code sent to your email.
            </p>
            <div class="input_field">
                <input type="text" name="otp" id="otp_code" placeholder="6-digit code"
                       maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                       style="text-align:center; letter-spacing:8px; font-size:20px;" required />
            </div>
            <button type="submit" id="otpBtn">
                <i class="fas fa-check-circle me-2"></i>Verify Code
            </button>
            <p style="margin-top: 12px;">
                <button type="button" id="resendBtn"
                        style="background:transparent; color:#0c7347; border:1px solid #0c7347; width:auto; padding:8px 18px;"
                        disabled>Resend code</button>
            </p>
            <p><a href="{{ route('password.forgot') }}">Use a different email</a></p>
        </form>

        {{-- STEP 3: new password (hidden until OTP verified) --}}
        <form id="resetForm" style="margin-top: 40px; display: none;">
            @csrf
            <div class="input_field">
                <input type="password" name="password" id="reset_password" placeholder="New Password (min 8, letter + number)" required />
                <input type="password" name="password_confirmation" id="reset_password_confirmation" placeholder="Confirm New Password" required />
            </div>
            <button type="submit" id="resetBtn">
                <i class="fas fa-key me-2"></i>Set New Password
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cooldownTimer = null;

    function startCooldown(seconds) {
        const btn = $('#resendBtn');
        clearInterval(cooldownTimer);
        let left = seconds;
        btn.prop('disabled', true).text('Resend code in ' + left + 's');
        cooldownTimer = setInterval(function () {
            left--;
            if (left <= 0) {
                clearInterval(cooldownTimer);
                btn.prop('disabled', false).text('Resend code');
            } else {
                btn.text('Resend code in ' + left + 's');
            }
        }, 1000);
    }

    function errorMessage(xhr) {
        if (xhr.status === 429) return 'Too many attempts. Please wait a minute and try again.';
        return xhr.responseJSON?.message || 'Something went wrong.';
    }

    // STEP 1: send code
    $('#verifyForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#verifyBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Sending...');

        $.ajax({
            url: "{{ route('password.verify') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                email: $('#verify_email').val(),
                cnic: $('#verify_cnic').val(),
            },
            success: function (res) {
                Swal.fire({ title: 'Check your email', text: res.message, icon: 'info', confirmButtonColor: '#0c7347' });
                $('#verifyForm').hide();
                $('#otpForm').fadeIn(200);
                $('#otp_code').focus();
                startCooldown(res.cooldown || 60);
            },
            error: function (xhr) {
                Swal.fire({ title: 'Failed', text: errorMessage(xhr), icon: 'error', confirmButtonColor: '#d33' });
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Send Verification Code');
            }
        });
    });

    // STEP 2: verify code
    $('#otpForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#otpBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Verifying...');

        $.ajax({
            url: "{{ route('password.otp.verify') }}",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', otp: $('#otp_code').val() },
            success: function (res) {
                clearInterval(cooldownTimer);
                Swal.fire({ title: 'Verified!', text: res.message, icon: 'success', confirmButtonColor: '#0c7347' });
                $('#otpForm').hide();
                $('#resetForm').fadeIn(200);
            },
            error: function (xhr) {
                Swal.fire({ title: 'Verification Failed', text: errorMessage(xhr), icon: 'error', confirmButtonColor: '#d33' });
                $('#otp_code').val('').focus();
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i>Verify Code');
            }
        });
    });

    // Resend code
    $('#resendBtn').on('click', function () {
        $.ajax({
            url: "{{ route('password.otp.resend') }}",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                Swal.fire({ title: 'Code sent', text: res.message, icon: 'success', confirmButtonColor: '#0c7347' });
                startCooldown(res.cooldown || 60);
            },
            error: function (xhr) {
                Swal.fire({ title: 'Could not resend', text: errorMessage(xhr), icon: 'error', confirmButtonColor: '#d33' });
                if (xhr.responseJSON?.cooldown) startCooldown(xhr.responseJSON.cooldown);
            }
        });
    });

    // STEP 3: set new password
    $('#resetForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#resetBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...');

        $.ajax({
            url: "{{ route('password.reset') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                password: $('#reset_password').val(),
                password_confirmation: $('#reset_password_confirmation').val(),
            },
            success: function (res) {
                Swal.fire({ title: 'Success!', text: res.message, icon: 'success', confirmButtonColor: '#0c7347' })
                    .then(() => { window.location.href = "{{ route('login') }}"; });
            },
            error: function (xhr) {
                Swal.fire({ title: 'Reset Failed', text: errorMessage(xhr), icon: 'error', confirmButtonColor: '#d33' });
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-key me-2"></i>Set New Password');
            }
        });
    });
</script>
@endpush