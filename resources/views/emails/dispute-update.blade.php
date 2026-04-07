@extends('emails.layout')

@section('content')
@php
    $actionColors = [
        'message' => '#1a73e8',
        'resolution_proposed' => '#ff9800',
        'resolution_accepted' => '#00a651',
        'resolution_rejected' => '#dc3545',
        'resolved' => '#00a651',
        'closed' => '#6c757d',
        'admin_resolved' => '#00a651',
    ];
    $actionIcons = [
        'message' => '💬',
        'resolution_proposed' => '🤝',
        'resolution_accepted' => '✅',
        'resolution_rejected' => '❌',
        'resolved' => '✅',
        'closed' => '🔒',
        'admin_resolved' => '⚖️',
    ];
@endphp

<h1 style="color: {{ $actionColors[$action] ?? '#333' }}">{{ $actionIcons[$action] ?? '📋' }} {{ $actionLabel }}</h1>

<p>Hello {{ $notifiable->name }},</p>

@if($action === 'message')
    <p>A new message has been added to your dispute for SafeTrade transaction <strong>#{{ $reference }}</strong>.</p>
@elseif($action === 'resolution_proposed')
    <p>A resolution has been proposed for your dispute regarding SafeTrade transaction <strong>#{{ $reference }}</strong>.</p>
@elseif($action === 'resolution_accepted')
    <p>The proposed resolution for dispute on SafeTrade transaction <strong>#{{ $reference }}</strong> has been accepted.</p>
@elseif($action === 'resolution_rejected')
    <p>The proposed resolution for dispute on SafeTrade transaction <strong>#{{ $reference }}</strong> has been rejected.</p>
@elseif($action === 'resolved')
    <p>The dispute for SafeTrade transaction <strong>#{{ $reference }}</strong> has been <strong style="color: #00a651;">resolved</strong>. Both parties accepted the resolution.</p>
@elseif($action === 'closed')
    <p>The dispute for SafeTrade transaction <strong>#{{ $reference }}</strong> has been <strong>closed</strong>.</p>
@elseif($action === 'admin_resolved')
    <p>An administrator has resolved the dispute for SafeTrade transaction <strong>#{{ $reference }}</strong>.</p>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
    <tr>
        <td class="card">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="card-header">Dispute Information</td>
                </tr>
                <tr>
                    <td class="card-body">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Transaction:</span>
                                    <span style="font-weight: 600;">#{{ $reference }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Vehicle:</span>
                                    <span style="font-weight: 600;">{{ $dispute->transaction?->vehicle_title ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Dispute Type:</span>
                                    <span>{{ ucfirst(str_replace('_', ' ', $dispute->type ?? 'general')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Status:</span>
                                    <span class="badge-{{ in_array($action, ['resolved', 'admin_resolved', 'resolution_accepted']) ? 'success' : (in_array($action, ['closed']) ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($dispute->status ?? 'open') }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if($details)
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td style="background-color: #f8f9fa; border-left: 4px solid {{ $actionColors[$action] ?? '#ffcc00' }}; padding: 16px 20px; border-radius: 0 8px 8px 0;">
            <p style="margin: 0; color: #333;">{!! nl2br(e($details)) !!}</p>
        </td>
    </tr>
</table>
@endif

@if(in_array($action, ['resolution_proposed']))
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td class="info-box highlight">
            <strong>Action Required:</strong> Please review the proposed resolution and accept or reject it in your dashboard.
        </td>
    </tr>
</table>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/dashboard/transactions/{{ $dispute->transaction_id }}" class="btn-primary" style="display: inline-block; padding: 14px 36px; text-decoration: none;">
                View Dispute
            </a>
        </td>
    </tr>
</table>

<p>Best regards,<br>AutoScout24 SafeTrade Team</p>
@endsection
