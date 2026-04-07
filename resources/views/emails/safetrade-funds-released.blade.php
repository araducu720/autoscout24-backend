@extends('emails.layout')

@section('content')
<h1>Funds Released</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $notifiable->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Congratulations! The funds for your completed SafeTrade transaction have been released.
</p>

<!-- Transaction Summary Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 16px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 13px; font-weight: 600;">
            Transaction Summary
        </td>
    </tr>
    <tr>
        <td style="padding: 0 20px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px; width: 130px; border-bottom: 1px solid #f4f4f4;">Reference</td>
                    <td style="padding: 12px 0; color: #333333; font-weight: 600; font-size: 14px; border-bottom: 1px solid #f4f4f4;">{{ $transaction->reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px; border-bottom: 1px solid #f4f4f4;">Vehicle</td>
                    <td style="padding: 12px 0; color: #333333; font-weight: 600; font-size: 14px; border-bottom: 1px solid #f4f4f4;">{{ $transaction->vehicle_title }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px; border-bottom: 1px solid #f4f4f4;">Amount Released</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f4f4f4;">
                        <span style="font-size: 22px; font-weight: 700; color: #00a651;">&euro;{{ number_format($transaction->vehicle_price, 2, ',', '.') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px;">Status</td>
                    <td style="padding: 12px 0;">
                        <span style="display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background-color: #d4edda; color: #155724;">
                            Completed
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Bank Transfer Info -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(0, 166, 81, 0.08); border-left: 3px solid #00a651; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #333333; font-size: 13px; line-height: 1.6;">
                <strong>Bank Transfer:</strong> The funds will be transferred to your registered bank account within 1-3 business days.
            </p>
        </td>
    </tr>
</table>

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/dashboard/transactions/{{ $transaction->id }}"
               style="display: inline-block; background-color: #00a651; color: #ffffff; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px;">
                View Transaction
            </a>
        </td>
    </tr>
</table>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Thank you for using AutoScout24 SafeTrade! We hope you had a great experience.
</p>

<p style="color: #666666; font-size: 14px; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
