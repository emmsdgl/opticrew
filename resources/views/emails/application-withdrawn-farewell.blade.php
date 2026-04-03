<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Withdrawal Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f4f8; padding: 32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Hero Section with Background Image --}}
                    <tr>
                        <td style="background-image: url('{{ asset('images/backgrounds/app-status-bg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; padding: 0; text-align: center; height: 260px;" bgcolor="#002573">
                        </td>
                    </tr>

                    {{-- Finnoys Logo --}}
                    <tr>
                        <td style="padding: 28px 0 12px; text-align: center;">
                            <img src="{{ asset('images/finnoys-text-logo-light.png') }}" alt="Fin-noys" width="140" style="width: 140px; max-width: 140px;">
                        </td>
                    </tr>

                    {{-- Main Heading --}}
                    <tr>
                        <td style="padding: 20px 20px 8px; text-align: center;">
                            <h1 style="margin: 10px 0px; font-size: 26px; font-weight: 700; color: #111827; line-height: 1.3;">
                                Your Application Has Been<br><span style="display: inline-block; background: linear-gradient(135deg, #9ca3af, #6b7280, #4b5563, #9ca3af); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; color: #6b7280; font-size: 26px; font-weight: 700;">Withdrawn</span>
                            </h1>
                            <p style="margin: 15px 16px; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                <span style="display: inline-block; background-color: rgba(107,114,128,0.1); color: #6b7280; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; vertical-align: middle; border: 1px solid rgba(107,114,128,0.2);">Application Withdrawn</span>
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 20px 40px 10px;">
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 0;">
                                Hello,
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 16px 0;">
                                We have received your withdrawal request for the <strong>{{ $application->job_title }}</strong> position at Fin-noys Cleaning Services. Your application has been successfully withdrawn.
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 16px 0;">
                                We're sorry to see you go. We truly appreciate your interest in joining our team and the time you invested in the application process.
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 16px 0;">
                                Should your circumstances change in the future, we warmly welcome you to explore new opportunities with us. Our doors are always open.
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 16px 0;">
                                We wish you the very best in your career journey.
                            </p>
                        </td>
                    </tr>

                    {{-- Application Details --}}
                    <tr>
                        <td style="padding: 10px 40px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Position Applied For</p>
                                        <p style="margin: 0; font-size: 16px; color: #111827; font-weight: 600;">{{ $application->job_title }}</p>
                                        @if($application->job_type)
                                            <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">{{ ucfirst(str_replace('-', ' ', $application->job_type)) }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Divider --}}
                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="height: 1px; background-color: #e5e7eb;"></div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 40px 16px; text-align: center;">
                            <p style="margin: 0 0 8px; font-size: 12px; color: #9ca3af; line-height: 1.6;">
                                This is an automated message. You will not receive any further emails regarding this application.<br>
                                If you have questions, contact us at
                                <a href="mailto:finnoys0823@gmail.com" style="color: #3b82f6; text-decoration: none; font-weight: 600;">finnoys0823@gmail.com</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Copyright --}}
                    <tr>
                        <td style="padding: 12px 40px 20px; text-align: center; border-top: 1px solid #f0f0f0;">
                            <p style="margin: 0; font-size: 11px; color: #c0c7d1;">
                                &copy; {{ date('Y') }} Finnoys. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
