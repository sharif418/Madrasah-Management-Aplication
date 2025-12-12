<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>মানি রিসিপ্ট - {{ $payment->receipt_no }}</title>
    <style>
        @page {
            size: A5;
            margin: 10mm;
        }

        body {
            font-family: 'Hind Siliguri', 'Noto Sans Bengali', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .receipt {
            border: 2px solid #059669;
            padding: 20px;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            color: #059669;
            margin: 0;
        }

        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }

        .receipt-title {
            text-align: center;
            background: #059669;
            color: white;
            padding: 8px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px 0;
        }

        .info-table .label {
            font-weight: bold;
            width: 35%;
        }

        .amount-box {
            background: #f0fdf4;
            border: 2px solid #059669;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .amount-box .amount {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
        }

        .amount-box .label {
            font-size: 12px;
            color: #666;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
        }

        .signature {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(5, 150, 105, 0.1);
            font-weight: bold;
            z-index: -1;
        }

        .receipt-no {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #059669;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="watermark">পরিশোধিত</div>

    <div class="receipt">
        <div class="receipt-no">
            রিসিপ্ট নং: {{ $payment->receipt_no }}
        </div>

        <div class="header">
            <h1>মাদরাসা ম্যানেজমেন্ট</h1>
            <h2>মানি রিসিপ্ট / Money Receipt</h2>
            <p style="margin: 5px 0; font-size: 11px;">তারিখ: {{ $payment->payment_date->format('d/m/Y') }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">ছাত্রের নাম:</td>
                <td>{{ $payment->student->name }}</td>
            </tr>
            <tr>
                <td class="label">ভর্তি নং:</td>
                <td>{{ $payment->student->admission_no }}</td>
            </tr>
            <tr>
                <td class="label">শ্রেণি:</td>
                <td>{{ $payment->student->class?->name }}
                    {{ $payment->student->section?->name ? '(' . $payment->student->section->name . ')' : '' }}</td>
            </tr>
            <tr>
                <td class="label">পিতার নাম:</td>
                <td>{{ $payment->student->father_name }}</td>
            </tr>
            <tr>
                <td class="label">ফি এর ধরণ:</td>
                <td>{{ $payment->studentFee?->feeStructure?->feeType?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">পেমেন্ট মাধ্যম:</td>
                <td>{{ \App\Models\FeePayment::paymentMethodOptions()[$payment->payment_method] ?? $payment->payment_method }}
                </td>
            </tr>
            @if($payment->transaction_id)
                <tr>
                    <td class="label">ট্রান্সাকশন আইডি:</td>
                    <td>{{ $payment->transaction_id }}</td>
                </tr>
            @endif
        </table>

        <div class="amount-box">
            <div class="label">প্রদত্ত টাকার পরিমাণ</div>
            <div class="amount">৳ {{ number_format($payment->amount, 2) }}</div>
            <div class="label">{{ numberToWords($payment->amount) }} টাকা মাত্র</div>
        </div>

        @if($payment->notes)
            <p><strong>মন্তব্য:</strong> {{ $payment->notes }}</p>
        @endif

        <div class="footer">
            <div class="signature">
                <div class="signature-line">
                    অভিভাবক/ছাত্রের স্বাক্ষর
                </div>
            </div>
            <div class="signature">
                <div class="signature-line">
                    গ্রহীতার স্বাক্ষর ও সিল
                </div>
            </div>
        </div>

        <p style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
            এই রিসিপ্ট কম্পিউটার জেনারেটেড। প্রিন্ট করার তারিখ: {{ now()->format('d/m/Y h:i A') }}
        </p>
    </div>
</body>

</html>

@php
    function numberToWords($number)
    {
        $words = [
            0 => 'শূন্য',
            1 => 'এক',
            2 => 'দুই',
            3 => 'তিন',
            4 => 'চার',
            5 => 'পাঁচ',
            6 => 'ছয়',
            7 => 'সাত',
            8 => 'আট',
            9 => 'নয়',
            10 => 'দশ',
            11 => 'এগারো',
            12 => 'বারো',
            13 => 'তেরো',
            14 => 'চৌদ্দ',
            15 => 'পনেরো',
            16 => 'ষোলো',
            17 => 'সতেরো',
            18 => 'আঠারো',
            19 => 'উনিশ',
            20 => 'বিশ',
            30 => 'ত্রিশ',
            40 => 'চল্লিশ',
            50 => 'পঞ্চাশ',
            60 => 'ষাট',
            70 => 'সত্তর',
            80 => 'আশি',
            90 => 'নব্বই',
            100 => 'শত',
        ];

        $num = (int) $number;

        if ($num < 21)
            return $words[$num] ?? '';
        if ($num < 100) {
            $tens = (int) ($num / 10) * 10;
            $units = $num % 10;
            return $words[$tens] . ($units ? ' ' . $words[$units] : '');
        }
        if ($num < 1000) {
            $hundreds = (int) ($num / 100);
            $remainder = $num % 100;
            return ($hundreds > 1 ? $words[$hundreds] . ' ' : '') . 'শত' . ($remainder ? ' ' . numberToWords($remainder) : '');
        }
        if ($num < 100000) {
            $thousands = (int) ($num / 1000);
            $remainder = $num % 1000;
            return numberToWords($thousands) . ' হাজার' . ($remainder ? ' ' . numberToWords($remainder) : '');
        }
        if ($num < 10000000) {
            $lakhs = (int) ($num / 100000);
            $remainder = $num % 100000;
            return numberToWords($lakhs) . ' লক্ষ' . ($remainder ? ' ' . numberToWords($remainder) : '');
        }

        return $num;
    }
@endphp