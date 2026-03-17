@php
    $badgeColor = match($application->status) {
        'reviewed' => '#3b82f6',
        'interview_scheduled' => '#8b5cf6',
        'hired' => '#22c55e',
        'rejected' => '#ef4444',
        default => '#6b7280',
    };

    $badgeBg = match($application->status) {
        'reviewed' => 'rgba(59,130,246,0.1)',
        'interview_scheduled' => 'rgba(139,92,246,0.1)',
        'hired' => 'rgba(34,197,94,0.1)',
        'rejected' => 'rgba(239,68,68,0.1)',
        default => 'rgba(107,114,128,0.1)',
    };

    $badgeBorder = match($application->status) {
        'reviewed' => 'rgba(59,130,246,0.3)',
        'interview_scheduled' => 'rgba(139,92,246,0.3)',
        'hired' => 'rgba(34,197,94,0.2)',
        'rejected' => 'rgba(239,68,68,0.2)',
        default => 'rgba(107,114,128,0.2)',
    };

    $auroraGradient = match($application->status) {
        'reviewed' => 'linear-gradient(135deg, #60a5fa, #3b82f6, #2563eb, #818cf8)',
        'interview_scheduled' => 'linear-gradient(135deg, #a78bfa, #8b5cf6, #7c3aed, #c084fc)',
        'hired' => 'linear-gradient(135deg, #4ade80, #22c55e, #16a34a, #34d399)',
        'rejected' => 'linear-gradient(135deg, #f87171, #ef4444, #dc2626, #fb923c)',
        default => 'linear-gradient(135deg, #9ca3af, #6b7280, #4b5563, #9ca3af)',
    };

    $profile = $application->applicant_profile;
    if (is_string($profile)) {
        $profile = json_decode($profile, true);
    }
    $applicantName = 'Applicant';
    if (is_array($profile) && !empty($profile['first_name'])) {
        $applicantName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
    }

    $appUrl = config('app.url'); // used in footer link
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
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
                                Your Application Status<br>Has Been <span style="display: inline-block; background: {{ $auroraGradient }}; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; color: {{ $badgeColor }}; font-size: 26px; font-weight: 700;">{{ $statusLabel }}</span>
                            </h1>
                            <p style="margin: 15px 16px; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                <span style="display: inline-block; background-color: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; vertical-align: middle; border: 1px solid {{ $badgeBorder }};">{{ $statusLabel }}</span>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        {{-- Status message summary --}}
                        <td style="padding: 4px 48px 24px; text-align: center;">
                            <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
                                {!! $statusMessage !!}
                            </p>
                        </td>
                    </tr>

                    {{-- Divider --}}
                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="height: 1px; background-color: #e5e7eb;"></div>
                        </td>
                    </tr>

                    {{-- Letter Body --}}
                    <tr>
                        <td style="padding: 24px 40px 8px;">
                            <p style="margin: 0 0 16px; font-size: 15px; color: #374151; line-height: 1.7;">
                                <strong>Dear {{ $applicantName }},</strong>
                            </p>
                            <p style="margin: 0 0 16px; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                We hope this message finds you well. We would like to inform you that the status of your application for the position of <strong>{{ $application->job_title }}</strong> at <strong>Finnoys</strong> has been updated.
                            </p>
                            <p style="margin: 0 0 16px; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                Thank you for your interest in joining <strong>Finnoys</strong>. We appreciate your time and effort in applying.
                            </p>
                        </td>
                    </tr>

                    @if($application->status === 'interview_scheduled' && $application->interview_date)
                    {{-- Interview Details --}}
                    <tr>
                        <td style="padding: 0 40px 16px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 15px 2px;">
                                        <p style="margin: 0 0 6px; font-size: 14px; color: #4b5563; font-weight: 700;">Interview Details</p>
                                        <p style="margin: 0; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                            <strong>Date:</strong> {{ $application->interview_date->format('l, F d, Y') }}<br>
                                            <strong>Time:</strong> {{ $application->interview_date->format('h:i A') }}
                                            @if($application->interview_duration)
                                                ({{ $application->interview_duration }} minutes)
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    @if($application->admin_notes)
                    {{-- Admin Notes --}}
                    <tr>
                        <td style="padding: 0 40px 16px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #eff6ff; border-radius: 10px; border-left: 4px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 6px; font-size: 12px; color: #3b82f6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Note from Finnoys</p>
                                        <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.7;">{{ $application->admin_notes }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    {{-- Sign-off --}}
                    <tr>
                        <td style="padding: 8px 40px 24px;">
                            <p style="margin: 0; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                <strong>Best regards,</strong><br>
                                <em>Admin</em><br>
                                <span style="color: #6b7280;">HR Department Head - Recruitment</span><br>
                                <span style="color: #6b7280;">Fin-noys</span>
                            </p>
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
                                If you have any questions or require assistance, please feel free to reply to<br>
                                <a href="mailto:finnoys0823@gmail.com" style="color: #3b82f6; text-decoration: none; font-weight: 600;">finnoys0823@gmail.com</a>
                                or visit our <a href="{{ $appUrl }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">FAQs</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Sub-footer --}}
                    <tr>
                        <td style="padding: 0 40px 12px; text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #c0c7d1; line-height: 1.5;">
                                You have received this email as a registered user of finnoys.com. You may unsubscribe in these emails through the settings.
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
