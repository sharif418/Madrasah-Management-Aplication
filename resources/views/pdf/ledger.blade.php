<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>খতিয়ান</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .container {
            padding: 15px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .institution-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }

        .report-title {
            font-size: 14px;
            color: #374151;
            margin-top: 3px;
        }

        .head-name {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .date-range {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
            font-size: 10px;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
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

        .total-row {
            background: #dbeafe;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
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
            <div class="report-title">খতিয়ান (Ledger) - {{ $type }}</div>
            <div class="head-name">{{ $headName }}</div>
            <div class="date-range">{{ $dateFrom }} থেকে {{ $dateTo }} | প্রস্তুত: {{ $date }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>তারিখ</th>
                    <th>বিবরণ</th>
                    <th>ভাউচার</th>
                    <th class="text-right">ডেবিট</th>
                    <th class="text-right">ক্রেডিট</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($entry['description'] ?? '-', 40) }}</td>
                        <td>{{ $entry['voucher'] }}</td>
                        <td class="text-right text-red">
                            {{ $entry['debit'] > 0 ? '৳' . number_format($entry['debit'], 0) : '-' }}</td>
                        <td class="text-right text-green">
                            {{ $entry['credit'] > 0 ? '৳' . number_format($entry['credit'], 0) : '-' }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3"><strong>মোট</strong></td>
                    <td class="text-right text-red"><strong>৳{{ number_format($summary['total_debit'], 0) }}</strong>
                    </td>
                    <td class="text-right text-green"><strong>৳{{ number_format($summary['total_credit'], 0) }}</strong>
                    </td>
                </tr>
                <tr class="total-row">
                    <td colspan="3"><strong>ব্যালেন্স</strong></td>
                    <td class="text-right" colspan="2"><strong>৳{{ number_format(abs($summary['balance']), 0) }}
                            {{ $summary['balance'] >= 0 ? 'Cr' : 'Dr' }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">স্বয়ংক্রিয়ভাবে তৈরি | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}</div>
    </div>
</body>

</html>