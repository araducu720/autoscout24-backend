<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'AutoScout24' }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* AutoScout24 Email Design System — Matching Frontend */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 0 0 4px 4px;
        }

        /* Header — matches frontend fixed dark header */
        .email-header {
            background-color: #333333;
            padding: 0 24px;
            height: 56px;
        }
        .email-header table {
            width: 100%;
            height: 56px;
        }
        .email-header .logo-text {
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            text-decoration: none;
        }
        .email-header .logo-text span {
            color: #f5f200;
        }
        .email-header .header-badge {
            display: inline-block;
            background-color: #f5f200;
            color: #333333;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            vertical-align: middle;
            margin-left: 10px;
        }

        /* Body */
        .email-body {
            padding: 32px 24px;
        }

        /* Typography — matches frontend Inter system */
        h1 {
            color: #333333;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 16px 0;
        }
        h2 {
            color: #333333;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 12px 0;
        }
        h3 {
            color: #333333;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
            margin: 0 0 8px 0;
        }
        p {
            margin: 0 0 14px 0;
        }
        .text-secondary {
            color: #666666;
        }
        .text-muted {
            color: #767676;
        }
        .text-small {
            font-size: 12px;
        }

        /* Buttons — matching frontend .as24-btn-* exactly */
        .btn {
            display: inline-block;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            mso-padding-alt: 0;
        }
        .btn-primary {
            background-color: #f5f200;
            color: #333333 !important;
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3);
        }
        .btn-secondary {
            background-color: #333333;
            color: #ffffff !important;
        }
        .btn-success {
            background-color: #00a651;
            color: #ffffff !important;
        }
        .btn-outline {
            background-color: #ffffff;
            color: #333333 !important;
            border: 1px solid #dcdcdc;
        }

        /* Card — matching frontend .as24-card */
        .card {
            background-color: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            overflow: hidden;
            margin: 16px 0;
        }
        .card-header {
            background-color: #333333;
            color: #ffffff;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .card-body {
            padding: 16px 20px;
        }

        /* Detail row — for transaction/order details */
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #f4f4f4;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #767676;
            font-size: 13px;
        }
        .detail-value {
            color: #333333;
            font-size: 14px;
            font-weight: 600;
        }

        /* Info boxes — matching frontend patterns */
        .info-box {
            background-color: #f4f4f4;
            border-radius: 4px;
            padding: 16px;
            margin: 16px 0;
        }
        .info-box.highlight {
            background-color: rgba(245, 242, 0, 0.1);
            border-left: 3px solid #f5f200;
        }
        .info-box.success {
            background-color: rgba(0, 166, 81, 0.08);
            border-left: 3px solid #00a651;
        }
        .info-box.warning {
            background-color: rgba(255, 152, 0, 0.08);
            border-left: 3px solid #ff9800;
        }
        .info-box.danger {
            background-color: rgba(231, 76, 60, 0.08);
            border-left: 3px solid #e74c3c;
        }
        .info-box.info {
            background-color: rgba(0, 102, 204, 0.06);
            border-left: 3px solid #0066cc;
        }

        /* Price amount — matching frontend .as24-price + green */
        .amount {
            font-size: 22px;
            font-weight: 700;
            color: #333333;
        }
        .amount-green {
            color: #00a651;
        }

        /* Badge — matching frontend .as24-badge */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-info { background-color: #cce5ff; color: #004085; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-pending { background-color: #f5f200; color: #333333; }

        /* Link — matching frontend .as24-link */
        a.link {
            color: #0066cc;
            text-decoration: none;
        }
        a.link:hover {
            color: #004499;
            text-decoration: underline;
        }

        /* Divider */
        .divider {
            border: 0;
            height: 1px;
            background-color: #dcdcdc;
            margin: 20px 0;
        }

        /* Stat card for weekly digest */
        .stat-row {
            background-color: #f4f4f4;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 8px;
        }
        .stat-number {
            font-size: 18px;
            font-weight: 700;
            color: #333333;
        }
        .stat-label {
            font-size: 13px;
            color: #666666;
        }

        /* Footer — matching frontend FooterBottom */
        .email-footer {
            background-color: #f4f4f4;
            border-top: 1px solid #dcdcdc;
            padding: 24px;
        }
        .footer-links {
            text-align: center;
            margin-bottom: 16px;
        }
        .footer-links a {
            color: #333333;
            text-decoration: none;
            font-size: 13px;
            font-weight: 300;
            margin: 0 8px;
        }
        .footer-links a:hover {
            color: #1e3a5f;
        }
        .footer-tagline {
            text-align: center;
            color: #333333;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 12px;
        }
        .footer-copyright {
            text-align: center;
            color: #767676;
            font-size: 11px;
            font-weight: 300;
            border-top: 1px solid #dcdcdc;
            padding-top: 16px;
            margin-top: 16px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 24px 16px !important;
            }
            .email-footer {
                padding: 20px 16px !important;
            }
            .btn {
                display: block !important;
                text-align: center !important;
            }
            h1 {
                font-size: 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <!-- Preheader text (hidden) -->
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">
        {{ $preheader ?? '' }}
    </div>

    <center>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 16px 8px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" class="email-wrapper" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 0 0 4px 4px;">
                    <!-- Header -->
                    <tr>
                        <td class="email-header" style="background-color: #333333; padding: 0 24px; height: 56px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" height="56">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}" style="text-decoration: none;">
                                            <span style="color: #ffffff; font-size: 20px; font-weight: 700; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; letter-spacing: -0.3px;">Auto<span style="color: #f5f200;">Scout24</span></span>
                                        </a>
                                    </td>
                                    <td style="vertical-align: middle; text-align: right;">
                                        <span style="display: inline-block; background-color: #f5f200; color: #333333; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px;">SafeTrade</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="email-body" style="padding: 32px 24px;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-footer" style="background-color: #f4f4f4; border-top: 1px solid #dcdcdc; padding: 24px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 12px;">
                                        <span style="color: #333333; font-size: 13px; font-weight: 500;">AutoScout24: the largest pan-European online car market.</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; padding-bottom: 16px;">
                                        <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/about" style="color: #333333; text-decoration: none; font-size: 13px; font-weight: 300; margin: 0 6px;">About</a>
                                        <span style="color: #dcdcdc;">|</span>
                                        <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/contact" style="color: #333333; text-decoration: none; font-size: 13px; font-weight: 300; margin: 0 6px;">Contact</a>
                                        <span style="color: #dcdcdc;">|</span>
                                        <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/imprint" style="color: #333333; text-decoration: none; font-size: 13px; font-weight: 300; margin: 0 6px;">Imprint</a>
                                        <span style="color: #dcdcdc;">|</span>
                                        <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/privacy" style="color: #333333; text-decoration: none; font-size: 13px; font-weight: 300; margin: 0 6px;">Privacy</a>
                                        <span style="color: #dcdcdc;">|</span>
                                        <a href="{{ config('app.frontend_url', 'https://www.autoscout24safetrade.com') }}/terms" style="color: #333333; text-decoration: none; font-size: 13px; font-weight: 300; margin: 0 6px;">Terms</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; border-top: 1px solid #dcdcdc; padding-top: 16px;">
                                        <span style="color: #767676; font-size: 11px; font-weight: 300;">&copy; Copyright {{ date('Y') }} by AutoScout24 GmbH. All Rights reserved.</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </center>
</body>
</html>
