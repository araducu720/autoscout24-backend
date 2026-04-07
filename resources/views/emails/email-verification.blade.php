@extends('emails.layout')

@section('content')
<h1>Verify Your Email</h1>

<p style="color: #666666; font-size: 15px; line-height: 1.6;">
    Hello {{ $user->name }},
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6;">
    Welcome to AutoScout24! Please verify your email address to activate your account and start using all features.
</p>

<!-- Verify Button -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 24px 0;">
    <tr>
        <td align="center">
            <a href="{{ $verificationUrl }}"
               style="display: inline-block; background-color: #f5f200; color: #333333; padding: 12px 32px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 4px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);">
                Verify Email Address
            </a>
        </td>
    </tr>
</table>

<!-- Expiry Info -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: rgba(245, 242, 0, 0.1); border-left: 3px solid #f5f200; border-radius: 0 4px 4px 0; margin: 16px 0;">
    <tr>
        <td style="padding: 14px 16px;">
            <p style="margin: 0; color: #333333; font-size: 13px;">
                <strong>Expires in 60 minutes.</strong> For security, this verification link will expire after 60 minutes.
            </p>
        </td>
    </tr>
</table>

<!-- Benefits Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #dcdcdc; border-radius: 4px; overflow: hidden; margin: 20px 0;">
    <tr>
        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px; font-size: 13px; font-weight: 600;">
            What you can do with a verified account
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 20px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top; width: 16px;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">List and sell vehicles on AutoScout24</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">Use SafeTrade secure payment protection</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">Save favorites and set price alerts</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6; vertical-align: top;">&bull;</td>
                    <td style="padding: 4px 0; color: #666666; font-size: 13px; line-height: 1.6;">Contact sellers and book test drives</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="color: #767676; font-size: 12px; line-height: 1.6; margin-top: 20px;">
    If the button doesn't work, copy and paste this link into your browser:<br>
    <a href="{{ $verificationUrl }}" style="color: #0066cc; word-break: break-all; font-size: 12px;">{{ $verificationUrl }}</a>
</p>

<p style="color: #767676; font-size: 12px; line-height: 1.6;">
    If you did not create an account, no action is required.
</p>

<p style="color: #666666; font-size: 14px; line-height: 1.6; margin-top: 24px;">
    Best regards,<br>
    <strong style="color: #333333;">AutoScout24 SafeTrade Team</strong>
</p>
@endsection
