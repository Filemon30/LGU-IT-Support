<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}?v=3">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/biringan.png') }}">

    <title>City of Biringan - EnchantaTech | Login</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'public/assets/css/landing.css',
        'public/assets/css/login.css'
    ])

    <script src="{{ asset('assets/js/auth.js') }}"></script>
</head>
<body class="min-h-screen">

    <x-header
        title="ENCHANTATECH"
        subtitle="City of Biringan"
        logo="{{ asset('assets/images/biringan.png') }}"
        background="#ffffff"
        textColor="#111827"
    />

    <div class="signin-page">

    <div class="signin-container">


        {{-- =====================================================
            LEFT SIDE
        ====================================================== --}}
        <div class="signin-left">

            <div class="signin-left-content">


                <h2 class="signin-left-title">
                    Your IT Support Control Center
                </h2>


                <p class="signin-left-description">
                    Manage IT Support tickets, monitor service requests, track IT activities, 
                    and keep technical support organized from one centralized platform.
                </p>

            </div>

        </div>



        {{-- =====================================================
            RIGHT SIDE
        ====================================================== --}}
        <div class="signin-right">

            <div class="signin-form-wrapper">


                {{-- =========================
                    LOGO
                ========================== --}}
                <div class="signin-logo">

                    <img
                        src="{{ asset('assets/images/biringan.png') }}"
                        alt="Biringan"
                    >

                </div>


                {{-- =========================
                    HEADER
                ========================== --}}
                <h1 class="signin-heading">
                    Sign In
                </h1>

                <p class="signin-subheading">
                    Welcome back! Sign in to continue.
                </p>



                {{-- =========================
                    LOGIN FORM
                ========================== --}}
                <form
                    id="login-form"
                    method="POST" 
                    action="{{ route('login.store') }}"
                >
                    @csrf


                    {{-- USERNAME --}}
                    <div class="signin-field">

                        <label
                            for="username"
                            class="signin-label"
                        >
                            Email
                        </label>


                        <div class="signin-input-wrapper">

                            <i
                                class="ti ti-user signin-input-icon"
                            ></i>


                            <input
                                type="text"
                                id="email"
                                name="email"
                                class="signin-input"
                                placeholder="juandelacruz@biringancity.gov.ph"
                                autocomplete="email"
                                :value="old('email')"
                                class="{{ $errors->has('email') ? 'input-error' : '' }}"
                                :error="$errors->has('email') ? $errors->first('email') : null"
                                required
                            >

                        </div>

                        <div id="username-error" class="signin-error">

                            <i class="ti ti-alert-circle"></i>

                            <span></span>

                        </div>

                    </div>



                    {{-- PASSWORD --}}
                    <div class="signin-field">

                        <label
                            for="password"
                            class="signin-label"
                        >
                            Password
                        </label>


                        <div class="signin-input-wrapper">

                            <i
                                class="ti ti-lock signin-input-icon"
                            ></i>


                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="signin-input {{ $errors->has('password') ? 'input-error' : '' }}"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                                :error="$errors->has('password') ? $errors->first('password') : null"
                            >

                        </div>

                        <div id="password-error" class="signin-error">

                            <i class="ti ti-alert-circle"></i>

                            <span></span>

                        </div>

                    </div>



                    {{-- FORGOT PASSWORD --}}
                    <div class="forgot-password-wrapper">

                        <a
                            class="forgot-password"
                            onclick="openModal('contact-admin')"
                        >
                            Forgot Password?
                        </a>

                    </div>



                    {{-- SIGN IN --}}
                    <button
                        type="submit"
                        id="signin-btn"
                        class="signin-button"
                    >

                    
                    <span class="signin-btn-text">
                        Sign In
                    </span>
                    <i class="ti ti-login signin-btn-icon"></i>

                        <span class="signin-btn-dots hidden">

                            <span></span>
                            <span></span>
                            <span></span>

                        </span>

                    </button>

                </form>



                {{-- =========================
                    SIGN UP
                ========================== --}}
                <div class="signup-footer">

                    <span>
                        Are you from City Offices/Barangay?
                    </span>

                    <a
                        href="{{route('submit.request')}}"
                        class="signup-link"
                    >
                        Submit Ticket.
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<x-modal_form
        id="contact-admin"
        title="Forgot password"
        icon="ti ti-alert-triangle"
        width="max-w-xs"
    >

        <div class="flex flex-col items-center justify-center text-center gap-2">

            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-orange-500/10 border border-orange-500/20">
                <i class="ti ti-alert-triangle text-3xl text-orange-400"></i>
            </div>

            <p class="text-xs text-gray-600 leading-relaxed">
                Please contact your administrator.
            </p>

        </div>

    </x-modal_form>



</body>
</html>