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

    <main class="w-[calc(100%-2rem)] max-w-sm sm:max-w-none sm:w-fit h-fit px-6 py-6 sm:px-10 sm:py-8 bg-white rounded-xl mx-auto my-4 sm:my-8 shadow-lg border border-gray-200">
        
        @if (! session('from_logout'))
        <div class="flex justify-start">
            <a
                href="{{ route('home') }}"
                title="Back"
                class="flex items-center gap-1.5 h-9 px-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
            >
                <i class="ti ti-arrow-narrow-left text-xl"></i>
                <span class="text-xs font-light"> Return </span>
            </a>
        </div>
        @endif

        <div class="flex items-center justify-center w-15 h-15 mx-auto mt-2 rounded-xl bg-blue-50 text-blue-600">
         <i class="ti ti-shield-lock text-3xl"> </i> 
        </div>

        <h1 class="text-xl font-bold text-center text-gray-900 mt-2"> Welcome Back!</h1>
        <p class="text-center text-xs text-gray-500 mt-2"> Sign in with your credentials to access the <br> EnchantaTech portal. </p>
        
        <form id="login-form" class="w-full sm:w-80 mt-10 space-y-4" method="POST" action="{{ route('login.store') }}">
            @csrf

            <x-input
                label="Email"
                placeholder="admin@biringancity.gov.ph"
                type="email"
                name="email"
                autocomplete="email"
                leftIcon="ti ti-mail"
                backgroundColor="#ffffff"
                focusColor="#2563eb"
                iconFocusColor="#2563eb"
                :value="old('email')"
                class="{{ $errors->has('email') ? 'input-error' : '' }}"
                 :error="$errors->has('email') ? $errors->first('email') : null"
            />

            <x-input
                class="mt-3 password-field {{ $errors->has('password') ? 'input-error' : '' }}"
                label="Password"
                placeholder="********"
                type="password"
                name="password"
                autocomplete="password"
                leftIcon="ti ti-lock"
                rightIcon="ti ti-eye-off"
                backgroundColor="#ffffff"
                focusColor="#2563eb"
                iconFocusColor="#2563eb"
                :error="$errors->has('password') ? $errors->first('password') : null"
            />

            <div class="flex justify-end mt-2">
                <a
                    onclick="openModal('contact-admin')"
                    class="inline text-xs text-blue-600 hover:text-blue-700 hover:underline transition-colors cursor-pointer"
                >
                    Forgot Password?
                </a>
            </div>

            

            <button
                type="submit"
                class="login-button w-full mt-3 flex items-center justify-center gap-2 rounded-xl text-sm font-semibold text-white"
            >
                Login
                <i class="ti ti-login"></i>
            </button>

            <p class="text-center text-xs text-gray-500 mt-2"> Are you from City Offices or Barangay? </p>

            <a href="{{ route('submit.request') }}" class="cta-button h-10 w-full flex items-center justify-center gap-2 rounded-xl text-xs font-semibold text-white">
                    <span class="text-sm"> Submit Ticket</span>
                    <i class="text-sm ti ti-send"></i>
            </a>

        </form>
    </main>


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
