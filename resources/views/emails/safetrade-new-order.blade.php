@extends('emails.layout')

@section('content')
<h1>New Vehicle Order</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $notifiable->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Great news! You have received a new order for your vehicle through AutoScout24 SafeTrade.
</p>

<!-- Order Details Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 16px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 13px; font-weight: 600;">
            Order Details
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
                    <td style="padding: 12px 0; color: #767676; font-size: 13px;">Price</td>
                    <td style="padding: 12px 0;">
                        <span style="font-size: 22px; font-weight: 700; color: #333333;">&euro;{{ number_format($transaction->vehicle_price, 2, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- SafeTrade Protection Info -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(245, 242, 0, 0.1); border-left: 3px solid #f5f200; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #333333; font-size: 13px; line-height: 1.6;">
                <strong>SafeTrade Protection:</strong> The buyer's payment will be held safely in escrow until delivery is confirmed. Your funds are fully protected.
            </p>
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
    Please prepare the vehicle for delivery and await payment confirmation.
</p>

<p style="color: #666666; font-size: 14px; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
