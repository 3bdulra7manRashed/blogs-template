{{-- Minimal error page - Used as fallback when main layout fails --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>خطأ - {{ config('app.name', 'المدونة') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background: #f5f0ea;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            direction: rtl;
        }
        .container {
            text-align: center;
            max-width: 500px;
        }
        .code {
            font-size: 100px;
            font-weight: 900;
            color: #1f1f1f;
            line-height: 1;
        }
        .title {
            font-size: 1.5rem;
            color: #1f1f1f;
            margin: 1rem 0;
        }
        .message {
            color: #6f6f6f;
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: #1f1f1f;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn:hover {
            background: #c37c54;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">@yield('code', '!')</div>
        <h1 class="title">@yield('title', 'حدث خطأ')</h1>
        <p class="message">@yield('message', 'عذراً، حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى لاحقاً.')</p>
        <a href="{{ url('/') }}" class="btn">العودة للرئيسية</a>
    </div>
</body>
</html>

