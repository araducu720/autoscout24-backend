@extends('emails.layout')

@section('content')
<h1>Your Weekly Digest</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $notifiable->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Here's a summary of your AutoScout24 SafeTrade activity this week:
</p>

@php
    $hasActivity = ($stats['new_messages'] ?? 0) > 0
        || ($stats['price_drops'] ?? 0) > 0
        || ($stats['new_listings'] ?? 0) > 0
        || ($stats['pending_transactions'] ?? 0) > 0;
@endphp

@if($hasActivity)
<!-- Stats -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 16px 0;">
    @if(($stats['new_messages'] ?? 0) > 0)
    <tr>
        <td style="padding: 10px 16px; background-color: #f4f4f4; border-radius: 4px; border-bottom: 4px solid #ffffff;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="width: 40px; text-align: center; color: #0066cc; font-size: 18px; font-weight: 700;">
                        {{ $stats['new_messages'] }}
                    </td>
                    <td style="color: #666666; font-size: 13px; padding-left: 12px;">
                        new message{{ $stats['new_messages'] > 1 ? 's' : '' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    @if(($stats['price_drops'] ?? 0) > 0)
    <tr>
        <td style="padding: 10px 16px; background-color: rgba(0, 166, 81, 0.08); border-radius: 4px; border-bottom: 4px solid #ffffff;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="width: 40px; text-align: center; color: #00a651; font-size: 18px; font-weight: 700;">
                        {{ $stats['price_drops'] }}
                    </td>
                    <td style="color: #666666; font-size: 13px; padding-left: 12px;">
                        price drop{{ $stats['price_drops'] > 1 ? 's' : '' }} on your watched vehicles
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    @if(($stats['new_listings'] ?? 0) > 0)
    <tr>
        <td style="padding: 10px 16px; background-color: #f4f4f4; border-radius: 4px; border-bottom: 4px solid #ffffff;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="width: 40px; text-align: center; color: #333333; font-size: 18px; font-weight: 700;">
                        {{ $stats['new_listings'] }}
                    </td>
                    <td style="color: #666666; font-size: 13px; padding-left: 12px;">
                        new listing{{ $stats['new_listings'] > 1 ? 's' : '' }} matching your saved searches
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    @if(($stats['pending_transactions'] ?? 0) > 0)
    <tr>
        <td style="padding: 10px 16px; background-color: rgba(255, 193, 7, 0.1); border-radius: 4px; border-bottom: 4px solid #ffffff;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="width: 40px; text-align: center; color: #856404; font-size: 18px; font-weight: 700;">
                        {{ $stats['pending_transactions'] }}
                    </td>
                    <td style="color: #666666; font-size: 13px; padding-left: 12px;">
                        pending transaction{{ $stats['pending_transactions'] > 1 ? 's' : '' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
</table>
@else
<!-- Empty State -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4; border-radius: 4px; margin: 16px 0;">
    <tr>
        <td style="padding: 24px; text-align: center;">
            <p style="margin: 0; color: #767676; font-size: 13px;">
                No new activity this week. Check back soon!
            </p>
        </td>
    </tr>
</table>
@endif

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/dashboard"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 10px 24px; font-size: 14px; font-weight: 500; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);">
                Go to Dashboard
            </a>
        </td>
    </tr>
</table>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Thank you for using AutoScout24 SafeTrade!
</p>

<p style="color: #666666; font-size: 14px; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
