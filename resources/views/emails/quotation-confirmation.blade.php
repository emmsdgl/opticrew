@php
    $services = $quotation->cleaning_services ?? [];
    $serviceList = !empty($services) ? implode(', ', $services) : 'N/A';
    $appUrl = config('app.url');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Request Received</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f4f8; padding: 32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Hero Section --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%); padding: 40px 40px 32px; text-align: center;">
                            <img src="{{ asset('images/finnoys-text-logo-dark.png') }}" alt="Fin-noys" width="140" style="width: 140px; max-width: 140px; margin-bottom: 20px;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; line-height: 1.3;">
                                Quotation Request Received
                            </h1>
                            <p style="margin: 12px 0 0; font-size: 14px; color: rgba(255,255,255,0.8); line-height: 1.5;">
                                Thank you for your interest in our cleaning services
                            </p>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding: 28px 40px 8px;">
                            <p style="margin: 0 0 16px; font-size: 15px; color: #374151; line-height: 1.7;">
                                <strong>Dear {{ $quotation->client_name }},</strong>
                            </p>
                            <p style="margin: 0 0 16px; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                We have received your quotation request and our team had reviewed the details. Attached you will find the detailed quote for the requested service(s).
                            </p>
                        </td>
                    </tr>

                    {{-- Divider --}}
                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="height: 1px; background-color: #e5e7eb;"></div>
                        </td>
                    </tr>

                    {{-- Request Summary --}}
                    <tr>
                        <td style="padding: 20px 40px;">
                            <p style="margin: 0 0 16px; font-size: 14px; color: #374151; font-weight: 700;">Your Request Summary</p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #4b5563;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280; width: 40%;">Cleaning Service</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #111827;">{{ $serviceList }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">Booking Type</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #111827;">{{ ucfirst($quotation->booking_type) }}</td>
                                </tr>
                                @if($quotation->property_type)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">Property Type</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #111827;">{{ ucfirst(str_replace('_', ' ', $quotation->property_type)) }}</td>
                                </tr>
                                @endif
                                @if($quotation->city)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">Location</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #111827;">
                                        {{ implode(', ', array_filter([$quotation->district, $quotation->city, $quotation->postal_code])) }}
                                    </td>
                                </tr>
                                @endif
                                @if($quotation->date_of_service)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">Preferred Date</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #111827;">{{ $quotation->date_of_service->format('F j, Y') }}</td>
                                </tr>
                                @endif
                                @if($quotation->type_of_urgency)
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280;">Urgency</td>
                                    <td style="padding: 8px 0; font-weight: 600; color: #111827;">{{ ucfirst($quotation->type_of_urgency) }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    {{-- PDF Attachment Note --}}
                    @if($quotation->cleaning_services && count($quotation->cleaning_services) > 0)
                    <tr>
                        <td style="padding: 0 40px 16px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #eff6ff; border-radius: 10px; border-left: 4px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 4px; font-size: 12px; color: #3b82f6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Attached Document</p>
                                        <p style="margin: 0; font-size: 13px; color: #374151; line-height: 1.6;">
                                            Please find the quotation details for <strong>{{ $serviceList }}</strong> attached to this email as a PDF document for your reference.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    {{-- Next Steps --}}
                    <tr>
                        <td style="padding: 8px 40px 24px;">
                            <p style="margin: 0 0 12px; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                <strong>What happens next?</strong>
                            </p>
                            <p style="margin: 0; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                Our team will review your request and prepare a detailed quotation tailored to your specific needs. You can expect to hear from us within 1-2 business days.
                            </p>
                        </td>
                    </tr>

                    {{-- Sign-off --}}
                    <tr>
                        <td style="padding: 8px 40px 24px;">
                            <p style="margin: 0; font-size: 14px; color: #4b5563; line-height: 1.7;">
                                <strong>Best regards,</strong><br>
                                <span style="color: #6b7280;">The Fin-noys Team</span><br>
                                <span style="color: #6b7280;">Professional Cleaning Services</span>
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
                                If you have any questions, please feel free to contact us at<br>
                                <a href="mailto:finnoys0823@gmail.com" style="color: #3b82f6; text-decoration: none; font-weight: 600;">finnoys0823@gmail.com</a>
                                or visit our <a href="{{ $appUrl }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">website</a>
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
