<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>দান রসিদ - {{ $donation->receipt_no }}</title>
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
            border: 2px solid #10b981;
            padding: 20px;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            color: #10b981;
            margin: 0;
        }

        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }

        .receipt-title {
            text-align: center;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 10px;
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
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }

        .info-table .label {
            font-weight: bold;
            width: 35%;
            color: #555;
        }

        .amount-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }

        .amount-box .amount {
            font-size: 28px;
            font-weight: bold;
            color: #059669;
        }

        .amount-box .label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .fund-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            margin-left: 5px;
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

        .receipt-no {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 11px;
        }

        .dua {
            text-align: center;
            font-style: italic;
            color: #059669;
            margin-top: 15px;
            padding: 10px;
            background: #f0fdf4;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="receipt-no">
            রসিদ নং: {{ $donation->receipt_no }}
        </div>

        <div class="header">
            <h1>মাদরাসা ম্যানেজমেন্ট</h1>
            <h2>দান-অনুদান রসিদ</h2>
            <p style="margin: 5px 0; font-size: 11px;">তারিখ: {{ $donation->date->format('d/m/Y') }}</p>
        </div>

        <div class="receipt-title">
            জাযাকাল্লাহু খাইরান - আল্লাহ আপনাকে উত্তম প্রতিদান দিন
        </div>

        <table class="info-table">
            <tr>
                <td class="label">দাতার নাম:</td>
                <td>{{ $donation->donor_name }}</td>
            </tr>
            @if($donation->donor_phone)
                <tr>
                    <td class="label">ফোন:</td>
                    <td>{{ $donation->donor_phone }}</td>
                </tr>
            @endif
            @if($donation->donor_address)
                <tr>
                    <td class="label">ঠিকানা:</td>
                    <td>{{ $donation->donor_address }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">ফান্ডের ধরণ:</td>
                <td>{{ \App\Models\Donation::fundTypeOptions()[$donation->fund_type] ?? $donation->fund_type }}</td>
            </tr>
            <tr>
                <td class="label">পেমেন্ট মাধ্যম:</td>
                <td>{{ \App\Models\Donation::paymentMethodOptions()[$donation->payment_method] ?? $donation->payment_method }}
                </td>
            </tr>
            @if($donation->transaction_id)
                <tr>
                    <td class="label">ট্রান্সাকশন আইডি:</td>
                    <td>{{ $donation->transaction_id }}</td>
                </tr>
            @endif
        </table>

        <div class="amount-box">
            <div class="label">দানের পরিমাণ</div>
            <div class="amount">৳ {{ number_format($donation->amount, 2) }}</div>
        </div>

        @if($donation->purpose)
            <p><strong>উদ্দেশ্য:</strong> {{ $donation->purpose }}</p>
        @endif

        <div class="dua">
            "যে ব্যক্তি আল্লাহর সন্তুষ্টির জন্য দান করে, তার প্রতিটি প্রদত্ত টাকা সাতশত গুণ বৃদ্ধি পায়।"
        </div>

        <div class="footer">
            <div class="signature">
                <div class="signature-line">
                    দাতার স্বাক্ষর
                </div>
            </div>
            <div class="signature">
                <div class="signature-line">
                    গ্রহীতার স্বাক্ষর ও সিল
                </div>
            </div>
        </div>

        <p style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
            এই রসিদ কম্পিউটার জেনারেটেড। প্রিন্ট করার তারিখ: {{ now()->format('d/m/Y h:i A') }}
        </p>
    </div>
</body>

</html>