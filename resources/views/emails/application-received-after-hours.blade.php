<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Received</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #0a1628 0%, #1a2d50 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 22px; margin: 0; font-weight: 600;">FINNOYS</h1>
                            <p style="color: #8899bb; font-size: 11px; margin: 4px 0 0; letter-spacing: 1px;">CLEANING SERVICES</p>
                        </td>
                    </tr>

                    {{-- Status Badge --}}
                    <tr>
                        <td style="padding: 30px 40px 10px; text-align: center;">
                            <span style="display: inline-block; background-color: #3b82f6; color: #ffffff; padding: 6px 20px; border-radius: 20px; font-size: 13px; font-weight: 600; letter-spacing: 0.5px;">
                                Application Received
                            </span>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 20px 40px 10px;">
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 0;">
                                Hello,
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 16px 0;">
                                Thank you for your interest in the <strong>{{ $application->job_title }}</strong> position at Fin-noys Cleaning Services. We have received your application successfully.
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 16px 0;">
                                Your application was submitted outside of our regular business hours. Our recruitment team will review your application and get back to you by <strong>{{ $responseEta }}</strong>.
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 16px 0;">
                                In the meantime, please feel free to explore other opportunities on our recruitment page.
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

                    {{-- ETA Box --}}
                    <tr>
                        <td style="padding: 0 40px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #eff6ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 6px; font-size: 12px; color: #3b82f6; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Expected Response Time</p>
                                        <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.6;">{{ $responseEta }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 40px 30px; border-top: 1px solid #e5e7eb;">
                            <p style="color: #9ca3af; font-size: 12px; line-height: 1.6; margin: 0; text-align: center;">
                                This is an automated message from Fin-noys Cleaning Services.<br>
                                Please do not reply to this email. If you have questions, contact us at
                                <a href="mailto:finnoys0823@gmail.com" style="color: #3b82f6; text-decoration: none;">finnoys0823@gmail.com</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
