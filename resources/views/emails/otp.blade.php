<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fin-noys Account Verification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Familjen+Grotesk:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Familjen Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-image:{{ asset('images/backgrounds/otp-bg.svg') }};

        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }

        .logo p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .header p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .title h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.3;
        }

        .description {
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .description p {
            margin: 0;
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        .description .warning {
            font-weight: 600;
            color: #1a1a1a;
        }

        .otp-code {
            text-align: center;
            margin: 40px 0;
        }

        .otp-code h1 {
            margin: 0;
            font-size: 48px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 8px;
        }

        .wave-decoration {
            text-align: center;
            margin-top: 60px;
        }

        .wave-decoration svg {
            width: 100%;
            max-width: 600px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/finnoys-text-logo.svg') }}"
                class="h-20 flex flex-col justify-center w-full sidebar-logo" alt="logo">

        </div>

        <div class="header">
            <p>Verify Your Account</p>
        </div>

        <div class="title font-bold">
            <h2>Fin-noys Account<br>Verification</h2>
        </div>

        <div class="description">
            <p>
                To finish signing up, please either copy and paste or enter<br>
                the One-Time Password (OTP) manually. This code will<br>
                expire in 10 minutes. <span class="warning">Please do not share it with anyone.</span>
            </p>
        </div>

        <div class="otp-code">
            <h1>{{$otp}}</h1>
        </div>

        <div class="wave-decoration">
            <svg viewBox="0 0 600 150" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="waveGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#A0E7F5;stop-opacity:0.3" />
                        <stop offset="100%" style="stop-color:#70D9EF;stop-opacity:0.5" />
                    </linearGradient>
                </defs>
                <path d="M0,75 Q150,25 300,75 T600,75 L600,150 L0,150 Z" fill="url(#waveGradient)" />
                <path d="M0,90 Q150,50 300,90 T600,90 L600,150 L0,150 Z" fill="url(#waveGradient)" opacity="0.6" />
            </svg>
        </div>
    </div>
</body>

</html>