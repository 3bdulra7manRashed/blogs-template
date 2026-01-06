<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة جديدة من نموذج التواصل</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #c37c54 0%, #a86845 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .email-body {
            padding: 30px;
        }
        .info-row {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #c37c54;
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }
        .info-value {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }
        .info-value a {
            color: #c37c54;
            text-decoration: none;
        }
        .info-value a:hover {
            text-decoration: underline;
        }
        .message-box {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.8;
        }
        .email-footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
        }
        .email-footer a {
            color: #c37c54;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>📧 رسالة جديدة من نموذج التواصل</h1>
            <p>{{ config('app.name') }}</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Sender Name -->
            <div class="info-row">
                <span class="info-label">👤 الاسم</span>
                <div class="info-value">{{ $senderName }}</div>
            </div>

            <!-- Sender Email -->
            <div class="info-row">
                <span class="info-label">📧 البريد الإلكتروني</span>
                <div class="info-value">
                    <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a>
                </div>
            </div>

            <!-- Message -->
            <div class="info-row">
                <span class="info-label">💬 نص الرسالة</span>
                <div class="info-value message-box">{{ $senderMessage }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>تم إرسال هذه الرسالة من نموذج التواصل في موقع <a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>
            <p>{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
