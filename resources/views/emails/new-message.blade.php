@extends('emails.layout')

@section('content')
<h1>New Message Received</h1>

<p>Hello {{ $notifiable->name }},</p>

<p>You have received a new message from <strong>{{ $senderName }}</strong>{{ $vehicleTitle ? " regarding <strong>{$vehicleTitle}</strong>" : '' }}.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
    <tr>
        <td style="background-color: #f8f9fa; border-left: 4px solid #ffcc00; padding: 16px 20px; border-radius: 0 8px 8px 0;">
            <p style="margin: 0 0 4px 0; font-size: 13px; color: #6c757d;">{{ $senderName }} wrote:</p>
            <p style="margin: 0; color: #333; font-style: italic;">"{{ Str::limit($messagePreview, 200) }}"</p>
        </td>
    </tr>
</table>

@if($vehicleTitle)
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td class="card">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="card-header">Conversation Details</td>
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
                                    <span style="color: #6c757d;">From:</span>
                                    <span style="font-weight: 600;">{{ $senderName }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/messages" class="btn-primary" style="display: inline-block; padding: 14px 36px; text-decoration: none;">
                View Messages
            </a>
        </td>
    </tr>
</table>

<p style="color: #6c757d; font-size: 13px;">You can reply directly in the app to continue the conversation.</p>

<p>Best regards,<br>AutoScout24 SafeTrade Team</p>
@endsection
