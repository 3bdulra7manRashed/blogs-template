<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>تسجيل الدخول - {{ config('app.name', 'المدونة') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, var(--brand-secondary) 0%, #fff 50%, var(--brand-secondary) 100%);
            min-height: 100vh;
            direction: rtl;
            position: relative;
            overflow-x: hidden;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Background Circles */
        .bg-decoration {
            position: fixed;
            border-radius: 50%;
            opacity: 0.08;
            pointer-events: none;
        }

        .bg-decoration-1 {
            width: 400px;
            height: 400px;
            background: var(--brand-accent);
            top: -120px;
            right: -120px;
        }

        .bg-decoration-2 {
            width: 300px;
            height: 300px;
            background: var(--brand-primary);
            bottom: -80px;
            left: -80px;
        }

        /* Logo */
        .logo {
            margin-bottom: 1.5rem;
        }

        .logo a {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--brand-primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .logo-dot {
            width: 8px;
            height: 8px;
            background: var(--brand-accent);
            border-radius: 50%;
        }

        /* Main Container */
        .login-container {
            max-width: 360px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        /* Title */
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand-primary);
            margin-bottom: 0.25rem;
        }

        .page-subtitle {
            font-size: 0.9rem;
            color: var(--brand-muted);
            margin-bottom: 1.5rem;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            text-align: right;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--brand-primary);
            margin-bottom: 0.35rem;
        }

        .form-input {
            width: 100%;
            padding: 0.7rem 0.875rem;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Cairo', sans-serif;
            transition: all 0.2s ease;
            background: #f9fafb;
            color: var(--brand-primary);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--brand-accent);
            background: white;
            box-shadow: 0 0 0 3px rgba(195, 124, 84, 0.1);
        }

        .form-input::placeholder {
            color: #aaa;
        }

        .form-error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.35rem;
        }

        .session-status {
            background: #ecfdf5;
            color: #047857;
            padding: 0.6rem 0.875rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        /* Checkbox & Forgot Password Row */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .checkbox-input {
            width: 16px;
            height: 16px;
            accent-color: var(--brand-accent);
            cursor: pointer;
        }

        .checkbox-label {
            color: var(--brand-muted);
            cursor: pointer;
        }

        .form-link {
            color: var(--brand-accent);
            text-decoration: none;
            font-weight: 500;
        }

        .form-link:hover {
            text-decoration: underline;
        }

        /* Button */
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            width: 100%;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            background: var(--brand-primary);
            color: white;
        }

        .btn:hover {
            background: #333;
            transform: translateY(-1px);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        /* Back Link */
        .back-home {
            margin-top: 1.25rem;
        }

        .back-home a {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--brand-muted);
            text-decoration: none;
            font-size: 0.85rem;
        }

        .back-home a:hover {
            color: var(--brand-accent);
        }

        .back-home svg {
            width: 16px;
            height: 16px;
        }

        /* Desktop */
        @media (min-width: 640px) {
            .logo {
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
                margin-bottom: 0;
            }

            .logo a {
                font-size: 1.3rem;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
</head>
<body>
    <div class="bg-decoration bg-decoration-1"></div>
    <div class="bg-decoration bg-decoration-2"></div>

    <div class="page-wrapper">
        <div class="logo animate-in">
            <a href="{{ url('/') }}">
                <span>{{ config('app.name', 'المدونة') }}</span>
                <span class="logo-dot"></span>
            </a>
        </div>

        <div class="login-container animate-in">
            <h1 class="page-title">تسجيل الدخول</h1>
            <p class="page-subtitle">مرحباً بك مجدداً!</p>

            <div class="form-card">
                @if (session('status'))
                    <div class="session-status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input id="email" class="form-input" type="email" name="email" 
                               value="{{ old('email') }}" required autofocus 
                               autocomplete="username" placeholder="example@email.com" dir="ltr">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input id="password" class="form-input" type="password" name="password"
                               required autocomplete="current-password" placeholder="••••••••" dir="ltr">
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="checkbox-group">
                            <input id="remember_me" type="checkbox" class="checkbox-input" name="remember">
                            <label for="remember_me" class="checkbox-label">تذكرني</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="form-link" href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                        @endif
                    </div>

                    <button type="submit" class="btn">
                        <span>تسجيل الدخول</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="back-home">
                <a href="{{ url('/') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>العودة للرئيسية</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
