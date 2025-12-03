<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'المدونة') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #1f1f1f;
            --brand-secondary: #f5f0ea;
            --brand-accent: #c37c54;
            --brand-muted: #6f6f6f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--brand-secondary) 0%, #fff 50%, var(--brand-secondary) 100%);
            min-height: 100vh;
            min-height: 100dvh;
            direction: rtl;
            position: relative;
            overflow-x: hidden;
        }

        /* Page Wrapper */
        .page-wrapper {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
        }

        /* Decorative Background Elements */
        .bg-decoration {
            position: fixed;
            border-radius: 50%;
            opacity: 0.08;
            pointer-events: none;
        }

        .bg-decoration-1 {
            width: 500px;
            height: 500px;
            background: var(--brand-accent);
            top: -150px;
            right: -150px;
        }

        .bg-decoration-2 {
            width: 350px;
            height: 350px;
            background: var(--brand-primary);
            bottom: -100px;
            left: -100px;
        }

        /* Logo */
        .logo {
            margin-bottom: 2rem;
            text-align: center;
        }

        .logo a {
            font-family: 'Cairo', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--brand-primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-dot {
            width: 8px;
            height: 8px;
            background: var(--brand-accent);
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Main Container */
        .error-container {
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        /* Icon Container */
        .icon-container {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--brand-accent) 0%, #d4956f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 40px rgba(195, 124, 84, 0.25);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 10px 40px rgba(195, 124, 84, 0.25);
            }
            50% {
                box-shadow: 0 15px 50px rgba(195, 124, 84, 0.35);
            }
        }

        .icon-container svg {
            width: 32px;
            height: 32px;
            color: white;
        }

        /* Error Code */
        .error-code {
            font-family: 'Cairo', sans-serif;
            font-size: clamp(100px, 20vw, 180px);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        /* Error Title */
        .error-title {
            font-family: 'Cairo', sans-serif;
            font-size: clamp(1.25rem, 4vw, 1.75rem);
            font-weight: 700;
            color: var(--brand-primary);
            margin-bottom: 1rem;
        }

        /* Error Message */
        .error-message {
            font-family: 'Cairo', sans-serif;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            color: var(--brand-muted);
            line-height: 1.9;
            margin-bottom: 2rem;
            padding: 0 0.5rem;
        }

        /* Buttons */
        .buttons-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
        }

        .btn {
            font-family: 'Cairo', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-primary) 0%, #333 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(31, 31, 31, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(31, 31, 31, 0.35);
        }

        .btn-secondary {
            font-family: 'Cairo', sans-serif;
            background: white;
            color: var(--brand-primary);
            border: 2px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-secondary:hover {
            border-color: var(--brand-accent);
            color: var(--brand-accent);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .btn svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .btn span {
            font-family: 'Cairo', sans-serif;
        }

        /* Helpful Links */
        .helpful-links {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            width: 100%;
        }

        .helpful-links-title {
            font-family: 'Cairo', sans-serif;
            font-size: 0.85rem;
            color: var(--brand-muted);
            margin-bottom: 0.75rem;
        }

        .helpful-links-list {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .helpful-links-list a {
            font-family: 'Cairo', sans-serif;
            color: var(--brand-primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
            padding: 0.25rem 0;
        }

        .helpful-links-list a:hover {
            color: var(--brand-accent);
        }

        /* Desktop Styles */
        @media (min-width: 640px) {
            .page-wrapper {
                padding: 2rem;
            }

            .logo {
                position: absolute;
                top: 2rem;
                right: 2rem;
                margin-bottom: 0;
            }

            .logo a {
                font-size: 1.4rem;
            }

            .icon-container {
                width: 80px;
                height: 80px;
                margin-bottom: 2rem;
            }

            .icon-container svg {
                width: 40px;
                height: 40px;
            }

            .error-message {
                padding: 0;
            }

            .buttons-container {
                flex-direction: row;
                justify-content: center;
                max-width: none;
                width: auto;
            }

            .btn {
                width: auto;
                padding: 0.875rem 2rem;
            }
        }

        @media (min-width: 768px) {
            .error-container {
                max-width: 550px;
            }

            .helpful-links-list {
                gap: 1.5rem;
            }
        }

        /* Animation for page load */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: slideUp 0.5s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.05s; opacity: 0; }
        .delay-2 { animation-delay: 0.1s; opacity: 0; }
        .delay-3 { animation-delay: 0.15s; opacity: 0; }
        .delay-4 { animation-delay: 0.2s; opacity: 0; }
        .delay-5 { animation-delay: 0.25s; opacity: 0; }

        /* Safe area for notched devices */
        @supports (padding: env(safe-area-inset-bottom)) {
            .page-wrapper {
                padding-bottom: max(1.5rem, env(safe-area-inset-bottom));
            }
        }
    </style>
</head>
<body>
    <!-- Decorative Background -->
    <div class="bg-decoration bg-decoration-1"></div>
    <div class="bg-decoration bg-decoration-2"></div>

    <div class="page-wrapper">
        <!-- Logo -->
        <div class="logo animate-in delay-1">
            <a href="{{ url('/') }}">
                <span>{{ config('app.name', 'المدونة') }}</span>
                <span class="logo-dot"></span>
            </a>
        </div>

        <!-- Main Content -->
        <div class="error-container">
            <!-- Icon -->
            <div class="icon-container animate-in delay-2">
                @yield('icon')
            </div>

            <!-- Error Code -->
            <div class="error-code animate-in delay-2">
                @yield('code')
            </div>

            <!-- Title -->
            <h1 class="error-title animate-in delay-3">
                @yield('title')
            </h1>

            <!-- Message -->
            <p class="error-message animate-in delay-3">
                @yield('message')
            </p>

            <!-- Buttons -->
            <div class="buttons-container animate-in delay-4 ">
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>العودة للرئيسية</span>
                </a>
                <button onclick="history.back()" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>الرجوع للخلف</span>
                </button>
            </div>

            <!-- Helpful Links -->
            <div class="helpful-links animate-in delay-5">
                <p class="helpful-links-title">روابط قد تساعدك:</p>
                <div class="helpful-links-list">
                    <a href="{{ url('/') }}">الصفحة الرئيسية</a>
                    <a href="{{ route('search') }}">البحث</a>
                    <a href="{{ route('contact') }}">تواصل معي</a>
                    <a href="{{ route('about') }}">عني</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
