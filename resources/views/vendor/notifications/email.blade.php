{{-- resources/views/vendor/notifications/email.blade.php --}}
<html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #333;
                background-color: #f4f7fa;
                margin: 0;
                padding: 20px;
            }

            .container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            }

            h1 {
                color: #4CAF50;
                font-size: 24px;
                margin-bottom: 20px;
            }

            p {
                font-size: 16px;
                line-height: 1.5;
                margin-bottom: 20px;
            }

            a.button {
                display: inline-block;
                background-color: #4CAF50;
                color: white;
                padding: 12px 20px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 20px;
            }

            a.button:hover {
                background-color: #45a049;
            }

            .subcopy {
                font-size: 14px;
                color: #777;
                text-align: center;
                margin-top: 30px;
                border-top: 1px solid #ddd;
                padding-top: 20px;
            }

            .subcopy a {
                color: #4CAF50;
                text-decoration: none;
            }

            .footer {
                font-size: 12px;
                color: #aaa;
                text-align: center;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Reset Password Notification</h1>
            
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <p>
                <a href="{{ route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]) }}" class="button">
                    Reset Password
                </a>
            </p>
            
            <p>This password reset link will expire in {{ config('auth.passwords.users.expire', 60) }} minutes.</p>
            
            <p>If you did not request a password reset, no further action is required.</p>
            
            <p>Regards,<br>{{ config('app.name') }}</p>

            <div class="subcopy">
                <p>
                    <strong>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</strong>
                </p>
                <p>
                    <a href="{{ route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]) }}">
                        {{ route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]) }}
                    </a>
                </p>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
