@extends('emails.layout')

@section('content')
<h1>Message Received</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $contactMessage->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Thank you for reaching out! We have received your message regarding <strong style="color: #333333;">{{ $vehicleTitle }}</strong> and our team will get back to you as soon as possible.
</p>

<!-- Original Message Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 20px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 13px; font-weight: 600;">
            Your Message
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 20px;">
            <p style="margin: 0; color: #333333; font-size: 14px; line-height: 1.6; font-style: italic;">
                "{{ $contactMessage->message }}"
            </p>
            <p style="margin: 10px 0 0 0; color: #767676; font-size: 12px;">
                Sent on {{ $contactMessage->created_at->format('F j, Y \a\t H:i') }}
            </p>
        </td>
    </tr>
</table>

<!-- Response Time Info -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(245, 242, 0, 0.1); border-left: 3px solid #f5f200; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #333333; font-size: 13px;">
                <strong>Response Time:</strong> We typically respond within 24 hours during business days.
            </p>
        </td>
    </tr>
</table>

@if($contactMessage->vehicle)
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/vehicles/{{ $contactMessage->vehicle_id }}"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);">
                View Vehicle
            </a>
        </td>
    </tr>
</table>
@endif

<p style="color: #666666; font-size: 14px; line-height: 1.6; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
