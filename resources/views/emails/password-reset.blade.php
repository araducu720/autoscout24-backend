@extends('emails.layout')

@section('content')
<h1>Reset Your Password</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $user->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    We received a request to reset the password for your AutoScout24 account associated with <strong style="color: #333333;">{{ $user->email }}</strong>.
</p>

<!-- Reset Button -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ $resetUrl }}"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 12px 32px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);">
                Reset Password
            </a>
        </td>
    </tr>
</table>

<!-- Expiry Info -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(245, 242, 0, 0.1); border-left: 3px solid #f5f200; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #333333; font-size: 13px;">
                <strong>Expires in 60 minutes.</strong> For security, this password reset link will expire after 60 minutes.
            </p>
        </td>
    </tr>
</table>

<!-- Security Notice -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 20px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 13px; font-weight: 600;">
            Security Notice
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 20px;">
            <p style="margin: 0 0 8px 0; color: #333333; font-size: 14px; line-height: 1.6;">
                If you did not request a password reset, no action is needed — your account is safe and this link will expire automatically.
            </p>
            <p style="margin: 0; color: #767676; font-size: 12px;">
                Never share this link with anyone. AutoScout24 will never ask for your password via email.
            </p>
        </td>
    </tr>
</table>

<p style="color: #767676; font-size: 12px; line-height: 1.6; margin-top: 20px;">
    If the button doesn't work, copy and paste this link into your browser:<br>
    <a href="{{ $resetUrl }}" style="color: #0066cc; word-break: break-all; font-size: 12px;">{{ $resetUrl }}</a>
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
