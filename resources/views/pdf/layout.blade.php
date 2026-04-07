<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title') - AutoScout24</title>
    <style>
        /* AutoScout24 PDF Styles */
        @page {
            margin: 20mm 15mm;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            background-color: #333333;
            color: #ffffff;
            padding: 15px 20px;
            margin: -20mm -15mm 20px -15mm;
            display: table;
            width: calc(100% + 30mm);
        }
        
        .header .logo {
            font-size: 18pt;
            font-weight: bold;
        }
        
        .header .logo span {
            color: #f5f200;
        }
        
        .header .document-type {
            float: right;
            font-size: 12pt;
            margin-top: 3px;
        }
        
        .footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #666666;
            border-top: 1px solid #dcdcdc;
            padding-top: 10px;
        }
        
        h1 {
            color: #333333;
            font-size: 16pt;
            margin-top: 0;
            border-bottom: 2px solid #f5f200;
            padding-bottom: 10px;
        }
        
        h2 {
            color: #333333;
            font-size: 12pt;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .info-box {
            background-color: #f4f4f4;
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .info-box.highlight {
            background-color: rgba(245, 242, 0, 0.15);
            border-left: 4px solid #f5f200;
        }
        
        .info-box.success {
            background-color: rgba(0, 166, 81, 0.1);
            border-left: 4px solid #00a651;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        table th {
            background-color: #333333;
            color: #ffffff;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 10px;
            border-bottom: 1px solid #dcdcdc;
        }
        
        table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .two-column .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .two-column .column:first-child {
            padding-right: 4%;
        }
        
        .amount {
            font-size: 14pt;
            font-weight: bold;
            color: #00a651;
        }
        
        .reference {
            font-family: 'DejaVu Sans Mono', monospace;
            background-color: #f4f4f4;
            padding: 3px 8px;
            border-radius: 3px;
        }
        
        .signature-box {
            border: 1px solid #dcdcdc;
            padding: 20px;
            margin-top: 30px;
            min-height: 80px;
        }
        
        .signature-line {
            border-top: 1px solid #333333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 9pt;
            color: #666666;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60pt;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-20 {
            margin-top: 20px;
        }
        
        .mb-10 {
            margin-bottom: 10px;
        }
        
        .small {
            font-size: 8pt;
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="header">
        <span class="logo">Auto<span>Scout24</span></span>
        <span class="document-type">@yield('document-type')</span>
    </div>
    
    <div class="content">
        @yield('content')
    </div>
    
    <div class="footer">
        <p>AutoScout24 GmbH | SafeTrade Document | Generated: {{ now()->format('d.m.Y H:i') }} | Page <span class="page-number"></span></p>
        <p class="small">This document was generated automatically by AutoScout24 SafeTrade system.</p>
    </div>
</body>
</html>
