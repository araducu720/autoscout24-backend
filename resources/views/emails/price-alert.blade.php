@extends('emails.layout')

@section('content')
<h1>Price Alert</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $notifiable->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    The price of a vehicle you're watching has changed.
</p>

<!-- Vehicle & Price Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 16px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 14px; font-weight: 600;">
            {{ $vehicle->title }}
        </td>
    </tr>
    <tr>
        <td style="padding: 0 20px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px; width: 120px; border-bottom: 1px solid #f4f4f4;">Previous Price</td>
                    <td style="padding: 12px 0; color: #999999; font-size: 14px; text-decoration: line-through; border-bottom: 1px solid #f4f4f4;">
                        &euro;{{ number_format($oldPrice, 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px; border-bottom: 1px solid #f4f4f4;">New Price</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f4f4f4;">
                        <span style="font-size: 22px; font-weight: bold; color: #00a651;">&euro;{{ number_format($newPrice, 2, ',', '.') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #767676; font-size: 13px;">Change</td>
                    <td style="padding: 12px 0; font-weight: 600;">
                        @if($diff > 0)
                            <span style="color: #00a651; font-size: 14px;">
                                &#9660; -&euro;{{ number_format($diff, 2, ',', '.') }} ({{ $percentChange }}%)
                            </span>
                        @else
                            <span style="color: #e74c3c; font-size: 14px;">
                                &#9650; +&euro;{{ number_format(abs($diff), 2, ',', '.') }} ({{ abs($percentChange) }}%)
                            </span>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Context Box -->
@if($diff > 0)
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(0, 166, 81, 0.08); border-left: 3px solid #00a651; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #155724; font-size: 13px; line-height: 1.6;">
                <strong>Price Drop</strong> &mdash; This vehicle is now &euro;{{ number_format($diff, 2, ',', '.') }} cheaper than before.
            </p>
        </td>
    </tr>
</table>
@else
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(245, 242, 0, 0.12); border-left: 3px solid #f5f200; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #856404; font-size: 13px; line-height: 1.6;">
                <strong>Price Increase</strong> &mdash; The price has increased by &euro;{{ number_format(abs($diff), 2, ',', '.') }}.
            </p>
        </td>
    </tr>
</table>
@endif

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/vehicles/{{ $vehicle->id }}"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);">
                View Vehicle
            </a>
        </td>
    </tr>
</table>

<!-- Divider -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 20px 0;">
    <tr>
        <td style="border-top: 1px solid #dcdcdc;">&nbsp;</td>
    </tr>
</table>

<p style="color: #767676; font-size: 12px; line-height: 1.6;">
    You can manage your price alerts in your <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/dashboard/alerts" style="color: #0066cc; text-decoration: none;">dashboard</a>.
</p>

<p style="color: #666666; font-size: 14px; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
