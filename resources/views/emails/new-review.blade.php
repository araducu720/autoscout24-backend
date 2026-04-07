@extends('emails.layout')

@section('content')
<h1>New Review Received</h1>

<p>Hello {{ $notifiable->name }},</p>

<p>Your vehicle <strong>{{ $vehicleTitle }}</strong> has received a new review!</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
    <tr>
        <td class="card">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="card-header">Review Details</td>
                </tr>
                <tr>
                    <td class="card-body">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding: 12px 0; text-align: center;">
                                    <span style="font-size: 28px; color: #ffcc00;">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </span>
                                    <br>
                                    <span style="font-size: 14px; color: #6c757d;">{{ $review->rating }} out of 5 stars</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Vehicle:</span>
                                    <span style="font-weight: 600;">{{ $vehicleTitle }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-row">
                                    <span style="color: #6c757d;">Date:</span>
                                    <span>{{ $review->created_at->format('F j, Y') }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if($review->comment)
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
    <tr>
        <td style="background-color: #f8f9fa; border-left: 4px solid #ffcc00; padding: 16px 20px; border-radius: 0 8px 8px 0;">
            <p style="margin: 0 0 4px 0; font-size: 13px; color: #6c757d;">Review comment:</p>
            <p style="margin: 0; color: #333; font-style: italic;">"{{ Str::limit($review->comment, 300) }}"</p>
        </td>
    </tr>
</table>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/vehicles/{{ $vehicle->id }}" class="btn-primary" style="display: inline-block; padding: 14px 36px; text-decoration: none;">
                View Vehicle
            </a>
        </td>
    </tr>
</table>

<p>Best regards,<br>AutoScout24 SafeTrade Team</p>
@endsection
