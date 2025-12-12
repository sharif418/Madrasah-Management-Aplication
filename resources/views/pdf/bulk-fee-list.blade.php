<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>বকেয়া ফি তালিকা</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .container { padding: 15px; }
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
        .meta-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .meta-item {
            display: table-cell;
            padding: 5px 10px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 10px;
        }
        td { font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-red { color: #dc2626; }
        .text-green { color: #059669; }
        .font-bold { font-weight: bold; }
        .total-row {
            background: #fef3c7;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
        @php
            $months = [1=>'জানুয়ারি', 2=>'ফেব্রুয়ারি', 3=>'মার্চ', 4=>'এপ্রিল', 5=>'মে', 6=>'জুন', 7=>'জুলাই', 8=>'আগস্ট', 9=>'সেপ্টেম্বর', 10=>'অক্টোবর', 11=>'নভেম্বর', 12=>'ডিসেম্বর'];
        @endphp
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="institution-name">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
            <div class="report-title">বকেয়া ফি তালিকা</div>
        </div>

        <div class="meta-info">
            <div class="meta-item"><strong>শ্রেণি:</strong> {{ $className ?? 'সকল' }}</div>
            <div class="meta-item"><strong>মাস/বছর:</strong> {{ ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'][$month - 1] }}, {{ $year }}</div>
            <div class="meta-item"><strong>তারিখ:</strong> {{ $date }}</div>
            <div class="meta-item"><strong>মোট বকেয়া:</strong> ৳{{ number_format($totalDue, 0) }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>আইডি</th>
                    <th>ছাত্রের নাম</th>
                    <th>অভিভাবক</th>
                    <th>মোবাইল</th>
                    <th>ফি টাইপ</th>
                    <th class="text-right">মোট</th>
                    <th class="text-right">পরিশোধ</th>
                    <th class="text-right">বকেয়া</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentFees as $index => $fee)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $fee->student->student_id }}</td>
                        <td class="font-bold">{{ $fee->student->name }}</td>
                        <td>{{ $fee->student->guardian->name ?? '-' }}</td>
                        <td>{{ $fee->student->guardian->phone ?? '-' }}</td>
                        <td>{{ $fee->feeStructure->feeType->name ?? '-' }}</td>
                        <td class="text-right">৳{{ number_format($fee->final_amount, 0) }}</td>
                        <td class="text-right text-green">৳{{ number_format($fee->paid_amount, 0) }}</td>
                        <td class="text-right text-red font-bold">৳{{ number_format($fee->due_amount, 0) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" class="text-right">মোট:</td>
                    <td class="text-right">৳{{ number_format($studentFees->sum('final_amount'), 0) }}</td>
                    <td class="text-right text-green">৳{{ number_format($studentFees->sum('paid_amount'), 0) }}</td>
                    <td class="text-right text-red font-bold">৳{{ number_format($totalDue, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            এই তালিকা স্বয়ংক্রিয়ভাবে তৈরি করা হয়েছে | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}
        </div>
    </div>
</body>
</html>
