<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('assets/images/biringan.png') }}"
    >

    <title>
        @yield('title', 'Track Request')
        - EnchantaTech
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: #fafafa;
            font-family: 'Poppins', sans-serif;
            color: #1f2937;
        }
    </style>

</head>

<body class="min-h-screen">

    <x-header
        title="ENCHANTATECH"
        subtitle="City of Biringan"
        logo="{{ asset('assets/images/biringan.png') }}"
        background="#ffffff"
        textColor="#111827"
    />

    <main class="track-container mt-8">

        @yield('content')

    </main>

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/submit.js') }}"></script>

</body>

</html>
