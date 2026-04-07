@extends('emails.layout')

@section('content')
<h1>Test Drive Confirmed</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $testDrive->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Great news! Your test drive request has been <strong style="color: #28a745;">confirmed</strong>.
</p>

<!-- Vehicle Info Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 16px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 13px; font-weight: 600;">
            {{ $vehicle->title }}
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 20px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 8px 0; color: #767676; font-size: 13px; width: 120px; border-bottom: 1px solid #f4f4f4;">Vehicle:</td>
                    <td style="padding: 8px 0; color: #333333; font-weight: 600; font-size: 14px; border-bottom: 1px solid #f4f4f4;">
                        {{ $vehicle->make->name ?? 'N/A' }} {{ $vehicle->model->name ?? '' }} ({{ $vehicle->year ?? 'N/A' }})
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #767676; font-size: 13px;">Price:</td>
                    <td style="padding: 8px 0; color: #333333; font-weight: 700; font-size: 18px;">
                        &euro;{{ number_format($vehicle->price, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Confirmed Schedule Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(40, 167, 69, 0.06); border: 1px solid rgba(40, 167, 69, 0.2); border-radius: 4px; margin: 16px 0;">
    <tr>
        <td style="padding: 16px 20px;">
            <h3 style="margin: 0 0 12px 0; color: #333333; font-size: 14px; font-weight: 600;">
                &#10003; Confirmed Schedule
            </h3>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 6px 0; color: #767676; font-size: 13px; width: 120px;">Date:</td>
                    <td style="padding: 6px 0; color: #333333; font-weight: 600; font-size: 14px;">
                        {{ $testDrive->preferred_date->format('l, F j, Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #767676; font-size: 13px;">Time:</td>
                    <td style="padding: 6px 0; color: #333333; font-weight: 600; font-size: 14px;">
                        {{ $testDrive->preferred_time->format('H:i') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- What to Bring Section -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4; border-radius: 4px; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0 0 8px 0; color: #333333; font-size: 13px; font-weight: 600;">
                Please remember to bring:
            </p>
            <ul style="margin: 0; padding-left: 20px; color: #666666; font-size: 13px; line-height: 1.8;">
                <li>Valid driving license</li>
                <li>Proof of identity (ID card or passport)</li>
            </ul>
        </td>
    </tr>
</table>

<!-- CTA Button -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url') }}/vehicles/{{ $vehicle->id }}"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);">
                View Vehicle
            </a>
        </td>
    </tr>
</table>

<p style="color: #666666; font-size: 13px; line-height: 1.6; margin-top: 20px;">
    If you need to reschedule or cancel, please contact us as soon as possible.
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Best regards,<br>
    <strong>AutoScout24 SafeTrade Team</strong>
</p>
@endsection
