<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }
        .header {
            background-color: #4B49AC;
            color: white;
            padding: 10px 20px;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }} - Test Email</h2>
        </div>
        
        <p>Hello,</p>
        
        <p>This is a test email from your application to verify that the email configuration is working properly.</p>
        
        <p>{{ $content }}</p>
        
        <p>If you received this email, it means your email configuration is correct!</p>
        
        <p>Details:</p>
        <ul>
            <li>Date/Time: {{ now()->format('Y-m-d H:i:s') }}</li>
            <li>Server: {{ request()->getHttpHost() }}</li>
        </ul>
        
        <p>Best regards,<br>{{ config('app.name') }} System</p>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
