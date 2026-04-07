@extends('emails.layout')

@section('content')
@php
    $titles = [
        'new' => 'New Order Received',
        'accepted' => 'Order Accepted',
        'rejected' => 'Order Declined',
        'cancelled' => 'Order Cancelled',
    ];
    $headingColors = [
        'new' => '#1a73e8',
        'accepted' => '#00a651',
        'rejected' => '#dc3545',
        'cancelled' => '#6c757d',
    ];
@endphp

<h1 style="color: {{ $headingColors[$action] ?? '#333' }}">{{ $titles[$action] ?? 'Order Update' }}</h1>

<p>Hello {{ $notifiable->name }},</p>

@if($action === 'new')
    <p>Great news! You have received a new order for your vehicle.</p>
@elseif($action === 'accepted')
    <p>Your order has been <strong style="color: #00a651;">accepted</strong> by the seller. You can now proceed with payment.</p>
@elseif($action === 'rejected')
    <p>Unfortunately, the seller has <strong style="color: #dc3545;">declined</strong> your order.</p>
@elseif($action === 'cancelled')
    <p>An order has been <strong>cancelled</strong>.</p>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
    <tr>
        <td class="card">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="card-header">Order Details</td>
                </tr>
                <tr>
                    <td class="card-body">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Vehicle:</span>
                                    <span style="font-weight: 600;">{{ $vehicleTitle }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Price:</span>
                                    <span style="font-weight: 700; color: #00a651; font-size: 18px;">€{{ number_format($order->total_price, 2, ',', '.') }}</span>
                                </td>
                            </tr>
                            @if($action === 'new')
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Buyer:</span>
                                    <span style="font-weight: 600;">{{ $order->buyer?->name ?? 'Unknown' }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Date:</span>
                                    <span>{{ $order->created_at->format('F j, Y \a\t H:i') }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if($action === 'rejected' && $reason)
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td class="info-box warning">
            <strong>Reason for decline:</strong><br>
            "{{ $reason }}"
        </td>
    </tr>
</table>
@endif

@if($order->message)
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td style="background-color: #f8f9fa; padding: 16px 20px; border-radius: 8px;">
            <p style="margin: 0 0 4px 0; font-size: 13px; color: #6c757d;">Buyer's message:</p>
            <p style="margin: 0; color: #333; font-style: italic;">"{{ $order->message }}"</p>
        </td>
    </tr>
</table>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/dashboard/transactions" class="btn-primary" style="display: inline-block; padding: 14px 36px; text-decoration: none;">
                View Order
            </a>
        </td>
    </tr>
</table>

<p>Best regards,<br>AutoScout24 SafeTrade Team</p>
@endsection
