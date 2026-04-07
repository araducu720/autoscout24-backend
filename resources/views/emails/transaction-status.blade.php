@extends('emails.layout')

@section('content')
@php
    $statusColors = [
        'confirmed' => '#00a651',
        'in_transit' => '#1a73e8',
        'delivered' => '#00a651',
        'completed' => '#00a651',
        'cancelled' => '#dc3545',
        'tracking_added' => '#1a73e8',
    ];
    $statusIcons = [
        'confirmed' => '✓',
        'in_transit' => '🚚',
        'delivered' => '📦',
        'completed' => '🎉',
        'cancelled' => '✕',
        'tracking_added' => '📋',
    ];
@endphp

<h1 style="color: {{ $statusColors[$status] ?? '#333' }}">{{ $statusIcons[$status] ?? '📋' }} {{ $statusLabel }}</h1>

<p>Hello {{ $notifiable->name }},</p>

@if($status === 'confirmed')
    <p>Your SafeTrade transaction has been <strong style="color: #00a651;">confirmed</strong>. The process is moving forward.</p>
@elseif($status === 'in_transit')
    <p>The vehicle is now <strong style="color: #1a73e8;">in transit</strong>.</p>
@elseif($status === 'tracking_added')
    <p>A tracking number has been added to your transaction.</p>
@elseif($status === 'delivered')
    <p>The vehicle has been marked as <strong style="color: #00a651;">delivered</strong>.</p>
@elseif($status === 'completed')
    <p>Congratulations! Your transaction has been <strong style="color: #00a651;">completed successfully</strong>.</p>
@elseif($status === 'cancelled')
    <p>Your transaction has been <strong style="color: #dc3545;">cancelled</strong>.</p>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
    <tr>
        <td class="card">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="card-header">Transaction Details</td>
                </tr>
                <tr>
                    <td class="card-body">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Reference:</span>
                                    <span style="font-weight: 600;">{{ $transaction->reference }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Vehicle:</span>
                                    <span style="font-weight: 600;">{{ $transaction->vehicle_title }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Amount:</span>
                                    <span style="font-weight: 700; color: #00a651;">€{{ number_format($transaction->vehicle_price, 2, ',', '.') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Status:</span>
                                    <span class="badge-{{ $status === 'cancelled' ? 'danger' : 'success' }}">{{ $statusLabel }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if($trackingNumber)
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td class="info-box info">
            <strong>📋 Tracking Number:</strong><br>
            <span style="font-size: 18px; font-weight: 700; font-family: monospace; letter-spacing: 1px;">{{ $trackingNumber }}</span>
        </td>
    </tr>
</table>
@endif

@if($status === 'cancelled' && $reason)
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td class="info-box warning">
            <strong>Reason for cancellation:</strong><br>
            "{{ $reason }}"
        </td>
    </tr>
</table>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/dashboard/transactions/{{ $transaction->id }}" class="btn-primary" style="display: inline-block; padding: 14px 36px; text-decoration: none;">
                View Transaction
            </a>
        </td>
    </tr>
</table>

<p>Best regards,<br>AutoScout24 SafeTrade Team</p>
@endsection
