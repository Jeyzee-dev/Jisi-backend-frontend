<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Legal Ease</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; }
        .container { max-width: 500px; margin: 0 auto; padding: 20px; }
        .header { background: #f59e0b; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { background: #fff; border: 1px solid #e5e7eb; padding: 30px 20px; }
        .text { font-size: 14px; color: #4b5563; margin: 12px 0; line-height: 1.6; }
        .code-box { background: #f3f4f6; border-left: 4px solid #f59e0b; padding: 16px; margin: 20px 0; }
        .code { font-size: 32px; font-weight: 700; color: #f59e0b; text-align: center; font-family: monospace; letter-spacing: 4px; }
        .code-expiry { font-size: 13px; color: #6b7280; text-align: center; margin-top: 8px; }
        .footer { font-size: 12px; color: #6b7280; text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Legal Ease</h1>
        </div>
        
        <div class="content">
            <p class="text">Hello,</p>
            
            <p class="text">Please use the verification code below to complete your email verification and activate your account.</p>
            
            <div class="code-box">
                <div class="code">{{ $code }}</div>
                <div class="code-expiry">This code expires in 30 minutes</div>
            </div>
            
            <p class="text">Enter this code on the verification page to proceed.</p>
            
            <p class="text">If you did not request this verification, please ignore this email.</p>
            
            <div class="footer">
                <p>&copy; {{ date('Y') }} Legal Ease. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>