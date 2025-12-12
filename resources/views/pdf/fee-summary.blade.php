<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>ফি সামারি রিপোর্ট</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .institution-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
        }

        .report-title {
            font-size: 16px;
            color: #374151;
            margin-top: 5px;
        }

        .report-date {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .summary-card.green {
            border-top: 3px solid #10b981;
        }

        .summary-card.red {
            border-top: 3px solid #ef4444;
        }

        .summary-card.blue {
            border-top: 3px solid #3b82f6;
        }

        .summary-card.purple {
            border-top: 3px solid #8b5cf6;
        }

        .summary-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }

        .summary-value.green {
            color: #10b981;
        }

        .summary-value.red {
            color: #ef4444;
        }

        .summary-value.blue {
            color: #3b82f6;
        }

        .summary-value.purple {
            color: #8b5cf6;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 11px;
        }

        td {
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #10b981;
        }

        .text-red {
            color: #ef4444;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-green {
            background: #d1fae5;
            color: #059669;
        }

        .badge-yellow {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e5e7eb;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, #10b981, #34d399);
            border-radius: 5px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="institution-name">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
            <div class="report-title">ফি সামারি রিপোর্ট -
                {{ $year }}{{ $month ? ' (' . ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'][$month - 1] . ')' : '' }}
            </div>
            <div class="report-date">প্রস্তুতের তারিখ: {{ $date }}</div>
        </div>

        {{-- Summary Cards --}}
        <div class="summary-cards">
            <div class="summary-card blue">
                <div class="summary-label">মোট নির্ধারিত</div>
                <div class="summary-value blue">৳{{ number_format($summary['total_assigned'], 0) }}</div>
            </div>
            <div class="summary-card green">
                <div class="summary-label">মোট আদায়</div>
                <div class="summary-value green">৳{{ number_format($summary['total_collected'], 0) }}</div>
                <div style="font-size: 10px; color: #6b7280;">{{ $summary['collection_rate'] }}% আদায়</div>
            </div>
            <div class="summary-card red">
                <div class="summary-label">মোট বকেয়া</div>
                <div class="summary-value red">৳{{ number_format($summary['total_due'], 0) }}</div>
            </div>
            <div class="summary-card purple">
                <div class="summary-label">মোট ছাত্র</div>
                <div class="summary-value purple">{{ $summary['total_students'] }} জন</div>
                <div style="font-size: 10px; color: #6b7280;">{{ $summary['paid_students'] }} জন পরিশোধ</div>
            </div>
        </div>

        {{-- Class-wise Summary --}}
        @if(count($classWise) > 0)
            <div class="section-title">📚 শ্রেণিভিত্তিক সামারি</div>
            <table>
                <thead>
                    <tr>
                        <th>শ্রেণি</th>
                        <th class="text-center">ছাত্র</th>
                        <th class="text-right">নির্ধারিত</th>
                        <th class="text-right">আদায়</th>
                        <th class="text-right">বকেয়া</th>
                        <th class="text-center">আদায় হার</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classWise as $item)
                        <tr>
                            <td><strong>{{ $item['class_name'] }}</strong></td>
                            <td class="text-center">{{ $item['student_count'] }} জন</td>
                            <td class="text-right">৳{{ number_format($item['total'], 0) }}</td>
                            <td class="text-right text-green">৳{{ number_format($item['collected'], 0) }}</td>
                            <td class="text-right text-red">৳{{ number_format($item['due'], 0) }}</td>
                            <td class="text-center">
                                <span
                                    class="badge {{ $item['rate'] >= 80 ? 'badge-green' : ($item['rate'] >= 50 ? 'badge-yellow' : 'badge-red') }}">
                                    {{ $item['rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Fee Type-wise Summary --}}
        @if(count($feeTypeWise) > 0)
            <div class="section-title">💰 ফি টাইপ অনুযায়ী সামারি</div>
            <table>
                <thead>
                    <tr>
                        <th>ফি টাইপ</th>
                        <th class="text-right">মোট নির্ধারিত</th>
                        <th class="text-right">আদায়</th>
                        <th class="text-center">আদায় হার</th>
                        <th style="width: 150px;">অগ্রগতি</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feeTypeWise as $item)
                        <tr>
                            <td><strong>{{ $item['fee_type'] }}</strong></td>
                            <td class="text-right">৳{{ number_format($item['total'], 0) }}</td>
                            <td class="text-right text-green">৳{{ number_format($item['collected'], 0) }}</td>
                            <td class="text-center">{{ $item['rate'] }}%</td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $item['rate'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Monthly Trend --}}
        @if(count($monthlyTrend) > 0)
            <div class="section-title">📊 মাসিক আদায় ট্রেন্ড</div>
            <table>
                <thead>
                    <tr>
                        <th>মাস</th>
                        <th class="text-right">আদায়</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyTrend as $item)
                        <tr>
                            <td>{{ $item['month'] }}</td>
                            <td class="text-right text-green"><strong>৳{{ number_format($item['total'], 0) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Footer --}}
        <div class="footer">
            এই রিপোর্টটি স্বয়ংক্রিয়ভাবে তৈরি করা হয়েছে | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}
        </div>
    </div>
</body>

</html>