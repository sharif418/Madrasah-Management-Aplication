<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>ব্যালেন্স শীট</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
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

        .as-of-date {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background: #f3f4f6;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .section-title.asset {
            background: #d1fae5;
            color: #065f46;
        }

        .section-title.liability {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .text-right {
            text-align: right;
        }

        .text-green {
            color: #059669;
        }

        .text-red {
            color: #dc2626;
        }

        .text-blue {
            color: #2563eb;
        }

        .total-row {
            font-weight: bold;
            border-top: 2px solid #374151;
            background: #f9fafb;
        }

        .net-worth {
            background: #2563eb;
            color: white;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }

        .net-worth .amount {
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="institution-name">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
            <div class="report-title">ব্যালেন্স শীট (Balance Sheet)</div>
            <div class="as-of-date">{{ $summary['as_of_date'] }} পর্যন্ত | প্রস্তুত: {{ $date }}</div>
        </div>

        {{-- Assets --}}
        <div class="section">
            <div class="section-title asset">সম্পদ (Assets)</div>
            <table>
                @foreach($assets as $asset)
                    <tr>
                        <td>{{ $asset['name'] }}</td>
                        <td class="text-right text-green">৳{{ number_format($asset['amount'], 0) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>মোট সম্পদ</strong></td>
                    <td class="text-right text-green"><strong>৳{{ number_format($summary['total_assets'], 0) }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Liabilities --}}
        <div class="section">
            <div class="section-title liability">দায় (Liabilities)</div>
            <table>
                @foreach($liabilities as $liability)
                    <tr>
                        <td>{{ $liability['name'] }}</td>
                        <td class="text-right text-red">৳{{ number_format($liability['amount'], 0) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>মোট দায়</strong></td>
                    <td class="text-right text-red">
                        <strong>৳{{ number_format($summary['total_liabilities'], 0) }}</strong></td>
                </tr>
            </table>
        </div>

        {{-- Net Worth --}}
        <div class="net-worth">
            <div>নিট সম্পদ (Net Worth)</div>
            <div class="amount">৳{{ number_format($summary['net_worth'], 0) }}</div>
        </div>

        <div class="footer">স্বয়ংক্রিয়ভাবে তৈরি | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}</div>
    </div>
</body>

</html>