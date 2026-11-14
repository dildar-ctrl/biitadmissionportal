<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Northern University Admission Test Portal"/>
    <meta name="keyword" content="Northern University Admission Test"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Northern University')</title>
    <link rel="icon" href="{{ asset('assets/Logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- ============================================= --}}
    {{-- SHARED THEME CSS — edit here, applies everywhere --}}
    {{-- ============================================= --}}
   <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Open Sans', sans-serif;
    }

    html, body {
    min-height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
}

   body {
    background-image: url({{ asset('login.png') }});
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    background-attachment: fixed;
    width: 100vw;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px 0;
    position: relative;
}

    /* Green shade overlay on background image */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #0c7347;
        opacity: 0.45;
        z-index: 1;
    }

    body > * {
        position: relative;
        z-index: 2;
    }

    .container {
        background-color: rgba(255, 255, 255, 0.733);
        padding: 60px 60px;
        border-radius: 15px;
        width: 450px;
          box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
        display: flex;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .container img {
        border-radius: 50%;
    }

    .input_field {
        display: flex;
        flex-direction: column;
    }

    .input_field input,
    .input_field select {
        height: 50px;
        width: 350px;
        margin-bottom: 20px;
        padding: 20px 10px;
        outline: none;
        background-color: rgba(255, 255, 255, 0.733);
        border: none;
        border: 2px solid #0c7347;
        transition: all 0.3s ease;
    }

    .input_field input:focus,
    .input_field select:focus {
        border-color: #0a5f3b;
        box-shadow: 0 0 5px rgba(12, 115, 71, 0.5);
        background-color: rgba(255, 255, 255, 0.9);
    }

    .input_field input::placeholder {
        font-size: 1.2rem;
        color: #0c7347;
        font-weight: 700;
    }

    button, .btn-theme {
        width: 100%;
        height: 50px;
        border: none;
        font-size: 1.4rem;
        font-weight: 700;
        background-color: #0c7347;
        color: white;
        margin-bottom: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    button:hover, .btn-theme:hover {
        background-color: #0a5f3b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(12, 115, 71, 0.3);
        color: white;
    }

    button:active {
        transform: translateY(0);
    }

    .container form p {
        text-align: center;
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .container form p a {
        text-decoration: none;
        color: #0c7347;
        opacity: 1;
        font-weight: 600;
    }

    .container form p a:hover {
        text-decoration: underline;
    }

    .center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-top: -30px;
        width: 30%;
    }

    .column {
        float: left;
        width: 50%;
        flex: 1;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .psw {
        float: right;
    }

    .register {
        font-weight: 700;
        font-size: 16.5px;
    }

    @media screen and (max-width: 450px) {
        .container {
            width: 90%;
            padding: 40px 25px;
        }
        .input_field input,
        .input_field select {
            width: 100%;
        }
        .row {
            flex-direction: column;
            display: inline;
        }
        .psw {
            text-align: right;
            font-size: 14px;
            margin-top: 3px;
        }
        .rmb {
            font-size: 14px;
            font-weight: 700;
        }
        .acc {
            font-size: 13px;
        }
        .register {
            font-size: 14px;
        }
    }

    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(12, 115, 71, 0.95), rgba(10, 95, 59, 0.95));
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: all 0.3s ease;
    }

    .preloader span {
        font-size: 30px;
        font-weight: bold;
        color: white;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    .hidden {
        display: none;
    }

    .alert-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        max-width: 400px;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .alert {
        animation: slideIn 0.3s ease;
    }

    /* SweetAlert2 safety-net + sizing (smaller than login card) */
    .swal2-container {
        position: fixed !important;
        inset: 0 !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        z-index: 99999 !important;
    }

    .swal2-popup {
        width: 360px !important;
        padding: 1.5em !important;
    }

    .swal2-title {
        font-size: 1.4rem !important;
    }

    .swal2-html-container {
        font-size: 0.95rem !important;
    }

    .swal2-icon {
        width: 60px !important;
        height: 60px !important;
        margin: 0.6em auto !important;
    }

    .swal2-confirm, .swal2-cancel {
        font-size: 0.9rem !important;
        padding: 0.5em 1.5em !important;
    }
</style>

    {{-- page-specific extra CSS, if any --}}
    @stack('styles')
</head>

<body>
    <div class="preloader" id="preloader">
        <div class="text-center">
            <span>Loading...</span>
            <div class="mt-3">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <div id="content" style="display: none; width: 100%;">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Hide preloader and show content after 1.5 seconds
            setTimeout(function() {
                $('#preloader').fadeOut(500, function() {
                    $('#content').fadeIn(500);
                });
            }, 1500);
        });

        // Display session messages with SweetAlert
        @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            timer: 3000,
            showConfirmButton: true,
            confirmButtonColor: '#3e4095'
        });
        @endif

        @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Try Again'
        });
        @endif

        @if($errors->any())
        Swal.fire({
            title: 'Form Error',
            text: '{{ $errors->first() }}',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
        @endif
    </script>

    {{-- page-specific extra JS, if any --}}
    @stack('scripts')
</body>

</html>