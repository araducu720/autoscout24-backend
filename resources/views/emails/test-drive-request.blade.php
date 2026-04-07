@extends('emails.layout')

@section('content')
<h1>New Test Drive Request</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $seller->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    You have received a new test drive request for your vehicle.
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

<!-- Requester Details Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(0, 102, 204, 0.06); border: 1px solid rgba(0, 102, 204, 0.15); border-radius: 4px; margin: 16px 0;">
    <tr>
        <td style="padding: 16px 20px;">
            <h3 style="margin: 0 0 12px 0; color: #333333; font-size: 14px; font-weight: 600;">
                Requester Details
            </h3>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 6px 0; color: #767676; font-size: 13px; width: 120px;">Name:</td>
                    <td style="padding: 6px 0; color: #333333; font-weight: 600; font-size: 14px;">{{ $testDrive->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #767676; font-size: 13px;">Email:</td>
                    <td style="padding: 6px 0;">
                        <a href="mailto:{{ $testDrive->email }}" style="color: #0066cc; text-decoration: none; font-size: 14px;">
                            {{ $testDrive->email }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #767676; font-size: 13px;">Phone:</td>
                    <td style="padding: 6px 0;">
                        <a href="tel:{{ $testDrive->phone }}" style="color: #0066cc; text-decoration: none; font-size: 14px;">
                            {{ $testDrive->phone }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #767676; font-size: 13px;">Preferred Date:</td>
                    <td style="padding: 6px 0; color: #333333; font-weight: 600; font-size: 14px;">
                        {{ $testDrive->preferred_date->format('l, F j, Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #767676; font-size: 13px;">Preferred Time:</td>
                    <td style="padding: 6px 0; color: #333333; font-weight: 600; font-size: 14px;">
                        {{ $testDrive->preferred_time->format('H:i') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if($testDrive->message)
<!-- Message -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4; border-radius: 4px; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0 0 6px 0; color: #767676; font-size: 12px; font-weight: 600;">
                Message:
            </p>
            <p style="margin: 0; color: #333333; font-size: 14px; line-height: 1.6; font-style: italic;">
                "{{ $testDrive->message }}"
            </p>
        </td>
    </tr>
</table>
@endif

<!-- Action Buttons -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url') }}/vehicles/{{ $vehicle->id }}"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3); margin-right: 8px;">
                View Vehicle
            </a>
            <a href="mailto:{{ $testDrive->email }}?subject=Re: Test Drive Request for {{ $vehicle->title }}"
               style="display: inline-block; background-color: #0066cc; color: #ffffff; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px;">
                Reply to Requester
            </a>
        </td>
    </tr>
</table>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Please contact the requester as soon as possible to confirm or reschedule the test drive appointment.
</p>

<!-- Safety Tips -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(255, 152, 0, 0.08); border-left: 3px solid #ff9800; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #856404; font-size: 12px; line-height: 1.6;">
                <strong>Safety Tips:</strong> Always verify the identity of the person requesting a test drive.
                Meet in a public place, bring a friend if possible, and ensure your vehicle has adequate insurance coverage.
            </p>
        </td>
    </tr>
</table>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Best regards,<br>
    <strong>AutoScout24 SafeTrade Team</strong>
</p>
@endsection
