<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>সার্কুলার - {{ $circular->circular_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        .container {
            padding: 20mm;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #1e40af;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .institution-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
        }

        .institution-address {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        .circular-title {
            margin-top: 25px;
            margin-bottom: 20px;
            text-align: center;
        }

        .circular-title h1 {
            font-size: 18px;
            border-bottom: 2px solid #1e40af;
            display: inline-block;
            padding-bottom: 5px;
        }

        .meta-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .meta-left,
        .meta-right {
            display: table-cell;
        }

        .meta-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-urgent {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-important {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-normal {
            background: #e0e7ff;
            color: #3730a3;
        }

        .subject {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .content {
            text-align: justify;
            line-height: 1.8;
        }

        .content p {
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 60px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 150px;
            margin: 0 auto;
            padding-top: 5px;
        }

        .print-date {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="institution-name">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
            <div class="institution-address">{{ institution_address() ?? 'ঠিকানা' }}</div>
            @if(function_exists('institution_phone'))
                <div class="institution-address">ফোন: {{ institution_phone() }}</div>
            @endif
        </div>

        <div class="circular-title">
            <h1>সার্কুলার</h1>
        </div>

        <div class="meta-info">
            <div class="meta-left">
                <strong>সার্কুলার নং:</strong> {{ $circular->circular_no }}<br>
                <strong>প্রাপক:</strong>
                {{ \App\Models\Circular::getAudienceOptions()[$circular->target_audience] ?? $circular->target_audience }}
            </div>
            <div class="meta-right">
                <strong>তারিখ:</strong> {{ $circular->issue_date?->format('d/m/Y') }}<br>
                @php
                    $priorityClass = match ($circular->priority) {
                        'urgent' => 'badge-urgent',
                        'important' => 'badge-important',
                        default => 'badge-normal',
                    };
                    $priorityLabel = match ($circular->priority) {
                        'urgent' => 'জরুরি',
                        'important' => 'গুরুত্বপূর্ণ',
                        default => 'সাধারণ',
                    };
                @endphp
                <span class="badge {{ $priorityClass }}">{{ $priorityLabel }}</span>
            </div>
        </div>

        <div class="subject">
            বিষয়: {{ $circular->title }}
        </div>

        <div class="content">
            {!! $circular->content !!}
        </div>

        @if($circular->effective_date)
            <div style="margin-top: 20px; font-style: italic;">
                <strong>কার্যকর তারিখ:</strong> {{ $circular->effective_date->format('d/m/Y') }}
            </div>
        @endif

        <div class="footer">
            <div class="signature-box">
                &nbsp;
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    প্রধান পরিচালক / অধ্যক্ষ<br>
                    {{ institution_name() ?? 'মাদরাসা নাম' }}
                </div>
            </div>
        </div>

        <div class="print-date">
            স্বয়ংক্রিয়ভাবে তৈরি | প্রস্তুত: {{ $date }}
        </div>
    </div>
</body>

</html>