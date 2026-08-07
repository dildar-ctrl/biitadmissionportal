@extends('layouts.auth')

@section('title', 'Create Account - Northern University')

@section('content')
<div class="container" style="width: 500px;">
    <form method="POST" action="{{ route('signup.submit') }}" style="width: 100%;">
        @csrf
        <img src="{{ asset('logo.png') }}" alt="NU Logo" class="center" />
        <h4 style="text-align: center; margin-top: 10px; color: #0c7347; font-size: 20px">
            Create Your Account <br />
            Barani Institute Admission
        </h4>
        <div class="input_field" style="margin-top: 40px;">
            <input type="text" name="fname" placeholder="First Name" value="{{ old('fname') }}" required />
            <input type="text" name="lname" placeholder="Last Name" value="{{ old('lname') }}" required />
            <input type="text" name="cnic" placeholder="CNIC (xxxxx-xxxxxxx-x)" value="{{ old('cnic') }}" required />
            <input type="text" name="mobile_no" placeholder="Mobile Number" value="{{ old('mobile_no') }}" required />
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            <input type="password" name="password" placeholder="Password" required />
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
        </div>
        <button type="submit" id="submitBtn">
            <i class="fas fa-user-plus me-2"></i>CREATE ACCOUNT
        </button>
        <p class="mt-2">
            Already have an account? <a href="{{ route('login') }}" class="register">Login here</a>
        </p>
    </form>
</div>
@endsection