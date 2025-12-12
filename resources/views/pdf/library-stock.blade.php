<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>লাইব্রেরি স্টক রিপোর্ট</title>
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

        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-item {
            display: table-cell;
            padding: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .summary-label {
            font-size: 9px;
            color: #6b7280;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 3px;
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

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #059669;
        }

        .text-orange {
            color: #ea580c;
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
            <div class="report-title">লাইব্রেরি স্টক রিপোর্ট</div>
            <div style="font-size: 10px; color: #6b7280; margin-top: 5px;">প্রস্তুত: {{ $date }}</div>
        </div>

        <div class="summary-box">
            <div class="summary-item">
                <div class="summary-label">মোট বই</div>
                <div class="summary-value">{{ $summary['total_books'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">মোট কপি</div>
                <div class="summary-value">{{ $summary['total_copies'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">সরবরাহযোগ্য</div>
                <div class="summary-value text-green">{{ $summary['available_copies'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">জারিকৃত</div>
                <div class="summary-value text-orange">{{ $summary['issued_copies'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">মোট মূল্য</div>
                <div class="summary-value">৳{{ number_format($summary['total_value'], 0) }}</div>
            </div>
        </div>

        <h3 style="font-size: 12px; margin-bottom: 5px;">ক্যাটাগরি অনুযায়ী স্টক</h3>
        <table>
            <thead>
                <tr>
                    <th>ক্যাটাগরি</th>
                    <th class="text-center">বই সংখ্যা</th>
                    <th class="text-center">মোট কপি</th>
                    <th class="text-center">সরবরাহযোগ্য</th>
                    <th class="text-center">জারিকৃত</th>
                    <th class="text-right">মূল্য</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryWise as $cat)
                    <tr>
                        <td>{{ $cat['name'] }}</td>
                        <td class="text-center">{{ $cat['book_count'] }}</td>
                        <td class="text-center">{{ $cat['total_copies'] }}</td>
                        <td class="text-center text-green">{{ $cat['available'] }}</td>
                        <td class="text-center text-orange">{{ $cat['issued'] }}</td>
                        <td class="text-right">৳{{ number_format($cat['value'], 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            স্বয়ংক্রিয়ভাবে তৈরি | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}
        </div>
    </div>
</body>

</html>