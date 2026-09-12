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
        - City of Biringan IT Support
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
            background: linear-gradient(
                90deg,
                #071f45,
                #020d1d,
                #071f45,
                #020d1d,
                #071f45
            );
            background-size: 300% 100%;
            animation: gradientPass 10s linear infinite;
            font-family: 'Poppins', sans-serif;
            color: #fff;
        }

        @keyframes gradientPass {
            0% {
                background-position: 0% 50%;
            }
            100% {
                background-position: 100% 50%;
            }
        }

        .track-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .track-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(63, 140, 255, 0.15);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
        }

        .track-icon {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: rgba(63, 140, 255, 0.1);
            border: 2px solid rgba(63, 140, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3f8cff;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 20px rgba(63, 140, 255, 0.3);
        }

        .track-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffffff;
            margin: 0 0 0.5rem;
        }

        .track-subtitle {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0 0 2rem;
        }

        .track-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .track-submit-btn {
            height: 2.8rem;
            background: linear-gradient(135deg, #3f8cff, #2c51ec);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .track-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(63, 140, 255, 0.7), 0 0 20px rgba(82, 120, 255, 0.6);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #ffffff;
        }

        .result-card {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(63, 140, 255, 0.15);
            border-radius: 12px;
            text-align: left;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .result-row:last-child {
            border-bottom: none;
        }

        .result-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
        }

        .result-value {
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-open {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-progress {
            background: rgba(234, 179, 8, 0.2);
            color: #eab308;
        }

        .status-resolved {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-closed {
            background: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
        }

        .input-error-message {
            display: none !important;
        }

        .floating-input label {
            color: #ffffff !important;
        }

        @keyframes radial-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes radial-pulse {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 1; }
        }

        .loading-radial {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            position: relative;
            animation: radial-spin 1.2s linear infinite;
        }

        .loading-logo-wrap {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 2rem;
            height: 2rem;
        }

        .loading-logo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: contain;
        }

        .loading-radial::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 4px solid transparent;
            border-top-color: #635BFF;
            border-right-color: #635BFF;
        }

        .loading-radial::after {
            content: '';
            position: absolute;
            inset: 6px;
            border-radius: 50%;
            border: 4px solid transparent;
            border-bottom-color: #a5a3ff;
            border-left-color: #a5a3ff;
            animation: radial-spin 0.8s linear infinite reverse;
        }

        .loading-text {
            animation: radial-pulse 1.5s ease-in-out infinite;
        }
    </style>

</head>

<body class="min-h-screen">

    <x-header
        title="IT SUPPORT"
        subtitle="City of Biringan"
        logo="{{ asset('assets/images/biringan.png') }}"
        background="rgba(9, 22, 40, 0.3)"
        textColor="#ffffff"
    />

    <main class="track-container mt-8">

        @yield('content')

    </main>

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/submit.js') }}"></script>

</body>

</html>
