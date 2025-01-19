<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .content {
            padding: 20px 0;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>

        <div class="content">
            <p>Hello {{ $user->first_name }},</p>

            <p>You have requested to reset your password. Click the button below to reset it:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>

            <p>This link will expire in 24 hours.</p>

            <p>If you didn't request this, please ignore this email or contact support if you have concerns.</p>
        </div>

        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
