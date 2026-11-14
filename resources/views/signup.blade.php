@extends('layouts.auth')

@section('title', 'Create Account - Barani Institute Of Information Technology')

@section('content')
<style>
    /* ---------- Base (desktop look stays the same) ---------- */
    .signup-wrap {
        box-sizing: border-box;
        width: 100%;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .signup-wrap form {
        width: 100%;
    }

    .signup-wrap img.center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        max-width: 100%;
        height: auto;
    }

    .signup-wrap .signup-title {
        text-align: center;
        margin-top: 10px;
        color: #0c7347;
        font-size: 20px;
        line-height: 1.35;
    }

    .signup-wrap .input_field {
        margin-top: 40px;
    }

    .signup-wrap .input_field input,
    .signup-wrap button {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .signup-wrap p {
        text-align: center;
        word-wrap: break-word;
    }

    /* ---------- Validation styles ---------- */
    .signup-wrap .field {
        width: 100%;
    }

    .signup-wrap .field-error {
        display: block;
        color: #c0392b;
        font-size: 13px;
        line-height: 1.3;
        margin: 4px 2px 8px;
        text-align: left;
    }

    .signup-wrap .field-error:empty {
        display: none;
    }

    .signup-wrap .input_field input.is-invalid {
        border-color: #c0392b !important;
    }

    .signup-wrap button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* ---------- Tablets and small laptops ---------- */
    @media (max-width: 768px) {
        .signup-wrap {
            width: calc(100% - 32px);
            margin-top: 16px;
            margin-bottom: 16px;
        }
    }

    /* ---------- Phones ---------- */
    @media (max-width: 576px) {
        .signup-wrap {
            /* Keep a gap on both sides so the card never touches the screen edge */
            width: calc(100% - 24px);
            padding-left: 16px;
            padding-right: 16px;
            margin-top: 12px;
            margin-bottom: 12px;
        }

        .signup-wrap img.center {
            /* Scale the logo down on small screens */
            width: clamp(90px, 28vw, 130px);
        }

        .signup-wrap .signup-title {
            font-size: clamp(16px, 4.6vw, 20px);
        }

        .signup-wrap .input_field {
            margin-top: 24px;
        }

        .signup-wrap .input_field input {
            /* 16px stops iOS Safari from zooming in when an input is focused */
            font-size: 16px;
            min-height: 46px;
        }

        .signup-wrap button {
            min-height: 48px;
            font-size: 16px;
        }
    }

    /* ---------- Very small phones ---------- */
    @media (max-width: 360px) {
        .signup-wrap {
            width: calc(100% - 16px);
            padding-left: 12px;
            padding-right: 12px;
        }

        .signup-wrap .input_field {
            margin-top: 18px;
        }
    }

    /* ---------- Landscape phones (short screens) ---------- */
    @media (max-height: 500px) and (orientation: landscape) {
        .signup-wrap img.center {
            width: 80px;
        }

        .signup-wrap .input_field {
            margin-top: 16px;
        }
    }
</style>

<div class="container signup-wrap">
    <form method="POST" action="{{ route('signup.submit') }}" id="signupForm" novalidate>
        @csrf
        <img src="{{ asset('logo.png') }}" alt="BIIT Logo" class="center" />
        <h4 class="signup-title">
            Create Your Account <br />
            Barani Institute Admission
        </h4>
        <div class="input_field">
            <div class="field">
                <input type="text" name="fname" placeholder="First Name" value="{{ old('fname') }}" maxlength="50" autocomplete="given-name" class="@error('fname') is-invalid @enderror" required />
                <small class="field-error" id="err-fname" role="alert">@error('fname'){{ $message }}@enderror</small>
            </div>
            <div class="field">
                <input type="text" name="lname" placeholder="Last Name" value="{{ old('lname') }}" maxlength="50" autocomplete="family-name" class="@error('lname') is-invalid @enderror" required />
                <small class="field-error" id="err-lname" role="alert">@error('lname'){{ $message }}@enderror</small>
            </div>
            <div class="field">
                <input type="text" name="cnic" placeholder="CNIC (xxxxx-xxxxxxx-x)" value="{{ old('cnic') }}" maxlength="15" inputmode="numeric" autocomplete="off" class="@error('cnic') is-invalid @enderror" required />
                <small class="field-error" id="err-cnic" role="alert">@error('cnic'){{ $message }}@enderror</small>
            </div>
            <div class="field">
                <input type="tel" name="mobile_no" placeholder="Mobile Number (03xxxxxxxxx)" value="{{ old('mobile_no') }}" maxlength="13" inputmode="tel" autocomplete="tel" class="@error('mobile_no') is-invalid @enderror" required />
                <small class="field-error" id="err-mobile_no" role="alert">@error('mobile_no'){{ $message }}@enderror</small>
            </div>
            <div class="field">
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" maxlength="100" autocomplete="email" class="@error('email') is-invalid @enderror" required />
                <small class="field-error" id="err-email" role="alert">@error('email'){{ $message }}@enderror</small>
            </div>
            <div class="field">
                <input type="password" name="password" placeholder="Password" maxlength="64" autocomplete="new-password" class="@error('password') is-invalid @enderror" required />
                <small class="field-error" id="err-password" role="alert">@error('password'){{ $message }}@enderror</small>
            </div>
            <div class="field">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" maxlength="64" autocomplete="new-password" required />
                <small class="field-error" id="err-password_confirmation" role="alert"></small>
            </div>
        </div>
        <button type="submit" id="submitBtn">
            <i class="fas fa-user-plus me-2"></i>CREATE ACCOUNT
        </button>
        <p class="mt-2">
            Already have an account? <a href="{{ route('login') }}" class="register">Login here</a>
        </p>
    </form>
</div>

<script>
    (function () {
        'use strict';

        var form = document.getElementById('signupForm');
        var submitBtn = document.getElementById('submitBtn');
        if (!form) { return; }

        var originalBtnHtml = submitBtn.innerHTML;

        var NAME_PATTERN = /^[A-Za-z][A-Za-z .'-]*$/;
        var CNIC_PATTERN = /^\d{5}-\d{7}-\d$/;
        var MOBILE_PATTERN = /^03\d{9}$/;
        var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

        /* ---------- Validators: return an error message, or '' when valid ---------- */
        function nameValidator(label) {
            return function (value) {
                value = value.trim();
                if (!value) { return label + ' is required.'; }
                if (value.length < 2) { return label + ' must be at least 2 characters.'; }
                if (value.length > 50) { return label + ' must not be longer than 50 characters.'; }
                if (!NAME_PATTERN.test(value)) {
                    return label + ' can only contain letters, spaces, dots, apostrophes and hyphens.';
                }
                return '';
            };
        }

        var validators = {
            fname: nameValidator('First name'),
            lname: nameValidator('Last name'),
            cnic: function (value) {
                if (!value) { return 'CNIC is required.'; }
                if (!CNIC_PATTERN.test(value)) { return 'Enter a valid 13-digit CNIC, e.g. 12345-1234567-1.'; }
                return '';
            },
            mobile_no: function (value) {
                if (!value) { return 'Mobile number is required.'; }
                if (!MOBILE_PATTERN.test(value)) { return 'Enter a valid mobile number, e.g. 03001234567.'; }
                return '';
            },
            email: function (value) {
                value = value.trim();
                if (!value) { return 'Email is required.'; }
                if (value.length > 100) { return 'Email must not be longer than 100 characters.'; }
                if (!EMAIL_PATTERN.test(value)) { return 'Enter a valid email address, e.g. name@example.com.'; }
                return '';
            },
            password: function (value) {
                if (!value) { return 'Password is required.'; }
                if (value.length < 8) { return 'Password must be at least 8 characters.'; }
                if (value.length > 64) { return 'Password must not be longer than 64 characters.'; }
                if (!/[A-Za-z]/.test(value)) { return 'Password must contain at least one letter.'; }
                if (!/\d/.test(value)) { return 'Password must contain at least one number.'; }
                return '';
            },
            password_confirmation: function (value) {
                if (!value) { return 'Please confirm your password.'; }
                if (value !== form.elements['password'].value) { return 'Passwords do not match.'; }
                return '';
            }
        };

        /* ---------- Formatters: clean the value while the user types ---------- */
        var formatters = {
            cnic: function (raw) {
                var d = raw.replace(/\D/g, '').slice(0, 13);
                if (d.length > 12) { return d.slice(0, 5) + '-' + d.slice(5, 12) + '-' + d.slice(12); }
                if (d.length > 5) { return d.slice(0, 5) + '-' + d.slice(5); }
                return d;
            },
            mobile_no: function (raw) {
                var d = raw.replace(/\D/g, '');
                // Convert +92 / 92 prefix into the local 03xx format
                if (d.indexOf('92') === 0 && d.length >= 12) { d = '0' + d.slice(2); }
                return d.slice(0, 11);
            }
        };

        var names = Object.keys(validators);
        var touched = {};

        function showError(input, message) {
            var errorEl = document.getElementById('err-' + input.name);
            if (errorEl) { errorEl.textContent = message; }
            input.classList.toggle('is-invalid', message !== '');
            input.setAttribute('aria-invalid', message !== '' ? 'true' : 'false');
        }

        function validateField(name) {
            var input = form.elements[name];
            var message = validators[name](input.value);
            showError(input, message);
            return message === '';
        }

        names.forEach(function (name) {
            var input = form.elements[name];

            input.addEventListener('input', function () {
                if (formatters[name]) { input.value = formatters[name](input.value); }
                // Re-check live once the field was visited or already shows an error
                if (touched[name] || input.classList.contains('is-invalid')) { validateField(name); }
                // Keep the confirm-password message in sync with the password field
                if (name === 'password' && touched['password_confirmation']) {
                    validateField('password_confirmation');
                }
            });

            input.addEventListener('blur', function () {
                if (name === 'email') { input.value = input.value.trim().toLowerCase(); }
                if (name === 'fname' || name === 'lname') { input.value = input.value.trim(); }
                touched[name] = true;
                validateField(name);
            });
        });

        form.addEventListener('submit', function (event) {
            var firstInvalid = null;

            names.forEach(function (name) {
                touched[name] = true;
                if (!validateField(name) && !firstInvalid) { firstInvalid = form.elements[name]; }
            });

            if (firstInvalid) {
                event.preventDefault();
                firstInvalid.focus();
                return;
            }

            // Prevent double submission
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating account...';
        });

        // Restore the button if the user comes back with the browser Back button
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        });
    })();
</script>
@endsection