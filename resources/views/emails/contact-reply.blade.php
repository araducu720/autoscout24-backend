@extends('emails.layout')

@section('content')
<h1>Reply to Your Message</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $contactMessage->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Thank you for contacting us about <strong style="color: #333333;">{{ $vehicleTitle }}</strong>. Here is our reply:
</p>

<!-- Reply Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(245, 242, 0, 0.1); border-left: 3px solid #f5f200; border-radius: 0 4px 4px 0; margin: 20px 0;">
    <tr>
        <td style="padding: 16px 20px;">
            <p style="margin: 0 0 8px 0; color: #767676; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                Our Reply
            </p>
            <div style="margin: 0; color: #333333; font-size: 14px; line-height: 1.7; white-space: pre-line;">{{ $replyBody }}</div>
        </td>
    </tr>
</table>

<hr style="border: 0; height: 1px; background-color: #dcdcdc; margin: 24px 0;">

<!-- Original Message -->
<p style="color: #767676; font-size: 12px; margin-bottom: 4px; font-weight: 600;">Your original message:</p>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4; border-radius: 4px; margin: 8px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.6; font-style: italic;">
                "{{ $contactMessage->message }}"
            </p>
            <p style="margin: 8px 0 0 0; color: #767676; font-size: 11px;">
                Sent on {{ $contactMessage->created_at->format('F j, Y \a\t H:i') }}
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

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    If you have any further questions, feel free to reply to this email.
</p>

<p style="color: #666666; font-size: 14px; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
