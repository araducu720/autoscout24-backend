@extends('emails.layout')

@section('content')
<h1>Dispute Opened</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $notifiable->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    A dispute has been opened for your SafeTrade transaction. Our team will review the case and work towards a fair resolution.
</p>

<!-- Dispute Details Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 16px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 13px; font-weight: 600;">
            Dispute Details
        </td>
    </tr>
    <tr>
        <td style="padding: 0 20px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px; width: 120px; border-bottom: 1px solid #f4f4f4;">Reference</td>
                    <td style="padding: 12px 0; color: #333333; font-weight: 600; font-size: 14px; border-bottom: 1px solid #f4f4f4;">{{ $transaction->reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px; border-bottom: 1px solid #f4f4f4;">Vehicle</td>
                    <td style="padding: 12px 0; color: #333333; font-weight: 600; font-size: 14px; border-bottom: 1px solid #f4f4f4;">{{ $transaction->vehicle_title }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px;">Status</td>
                    <td style="padding: 12px 0;">
                        <span style="display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background-color: #f8d7da; color: #721c24;">
                            Dispute Open
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Reason Box -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(255, 152, 0, 0.08); border-left: 3px solid #ff9800; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0 0 6px 0; color: #856404; font-size: 12px; font-weight: 600;">
                Reason for Dispute:
            </p>
            <p style="margin: 0; color: #333333; font-size: 14px; line-height: 1.6;">
                "{{ $reason }}"
            </p>
        </td>
    </tr>
</table>

<!-- What happens next -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4; border-radius: 4px; margin: 16px 0;">
    <tr>
        <td style="padding: 16px;">
            <h3 style="margin: 0 0 10px 0; color: #333333; font-size: 14px; font-weight: 600;">What happens next?</h3>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top; width: 16px;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">Our team will review the dispute within 24-48 hours</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">Both parties will be contacted for additional information</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">Funds remain held securely in escrow until resolved</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">A fair resolution will be reached based on the evidence</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/dashboard/transactions/{{ $transaction->id }}"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);">
                View Transaction
            </a>
        </td>
    </tr>
</table>

<p style="color: #666666; font-size: 13px; line-height: 1.6;">
    If you have any questions or additional information to provide, please contact our support team.
</p>

<p style="color: #666666; font-size: 14px; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
