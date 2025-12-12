<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>ক্যাশ বুক</title>
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

        .date-range {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .summary-label {
            font-size: 10px;
            color: #6b7280;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 3px;
        }

        .credit {
            color: #059669;
        }

        .debit {
            color: #dc2626;
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
            <div class="report-title">ক্যাশ বুক (দৈনিক হিসাব)</div>
            <div class="date-range">{{ $dateFrom }} থেকে {{ $dateTo }} | প্রস্তুত: {{ $date }}</div>
        </div>

        <div class="summary-box">
            <div class="summary-item">
                <div class="summary-label">প্রারম্ভিক জের</div>
                <div class="summary-value">৳{{ number_format($summary['opening_balance'], 0) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">মোট জমা</div>
                <div class="summary-value credit">৳{{ number_format($summary['total_credit'], 0) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">মোট খরচ</div>
                <div class="summary-value debit">৳{{ number_format($summary['total_debit'], 0) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">সমাপনী জের</div>
                <div class="summary-value">৳{{ number_format($summary['closing_balance'], 0) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>তারিখ</th>
                    <th>বিভাগ</th>
                    <th>বিবরণ</th>
                    <th>রেফারেন্স</th>
                    <th class="text-right">জমা (Cr)</th>
                    <th class="text-right">খরচ (Dr)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="total-row">
                    <td colspan="4"><strong>প্রারম্ভিক জের</strong></td>
                    <td class="text-right" colspan="2">
                        <strong>৳{{ number_format($summary['opening_balance'], 0) }}</strong></td>
                </tr>
                @foreach($transactions as $txn)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($txn['date'])->format('d/m/Y') }}</td>
                        <td>{{ $txn['category'] }}</td>
                        <td>{{ Str::limit($txn['description'], 35) }}</td>
                        <td>{{ $txn['reference'] }}</td>
                        <td class="text-right credit">
                            {{ $txn['type'] === 'credit' ? '৳' . number_format($txn['amount'], 0) : '-' }}</td>
                        <td class="text-right debit">
                            {{ $txn['type'] === 'debit' ? '৳' . number_format($txn['amount'], 0) : '-' }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4"><strong>মোট</strong></td>
                    <td class="text-right credit"><strong>৳{{ number_format($summary['total_credit'], 0) }}</strong>
                    </td>
                    <td class="text-right debit"><strong>৳{{ number_format($summary['total_debit'], 0) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="4"><strong>সমাপনী জের</strong></td>
                    <td class="text-right" colspan="2">
                        <strong>৳{{ number_format($summary['closing_balance'], 0) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            স্বয়ংক্রিয়ভাবে তৈরি | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}
        </div>
    </div>
</body>

</html>