<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>বকেয়া রিপোর্ট</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .summary {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #333;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background: #e0e0e0 !important;
        }

        .danger {
            color: #dc2626;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ setting('institution_name') ?: 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}</h1>
        <h2>ফি বকেয়া রিপোর্ট</h2>
    </div>

    <div class="summary">
        <span class="summary-item"><strong>তারিখ:</strong> {{ $date }}</span>
        <span class="summary-item"><strong>মোট বকেয়াদার:</strong> {{ $totalStudents }} জন</span>
        <span class="summary-item"><strong>মোট বকেয়া:</strong> <span
                class="danger">৳{{ number_format($totalDue, 2) }}</span></span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="10%">আইডি</th>
                <th width="20%">ছাত্রের নাম</th>
                <th width="10%">শ্রেণি</th>
                <th width="15%">ফি টাইপ</th>
                <th width="12%" class="amount">মোট</th>
                <th width="12%" class="amount">পরিশোধ</th>
                <th width="12%" class="amount">বকেয়া</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($dues as $index => $due)
                @php $dueAmount = $due->amount - $due->paid_amount;
                $grandTotal += $dueAmount; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $due->student->student_id ?? '' }}</td>
                    <td>{{ $due->student->name ?? '' }}</td>
                    <td>{{ $due->student->class->name ?? '' }}</td>
                    <td>{{ $due->feeType->name ?? '' }}</td>
                    <td class="amount">৳{{ number_format($due->amount, 2) }}</td>
                    <td class="amount">৳{{ number_format($due->paid_amount, 2) }}</td>
                    <td class="amount danger">৳{{ number_format($dueAmount, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" style="text-align: right;">মোট বকেয়া:</td>
                <td class="amount danger">৳{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>এই রিপোর্টটি {{ now()->format('d/m/Y h:i A') }} তারিখে তৈরি করা হয়েছে।</p>
    </div>
</body>

</html>