<!DOCTYPE html>
<html lang="bn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>পে-স্লিপ #{{ $payment->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Bengali', 'Kalpurush', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            background: #fff;
        }

        .payslip {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border: 2px solid #2c3e50;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
            color: #7f8c8d;
        }

        .header .title {
            font-size: 14px;
            color: #27ae60;
            margin-top: 10px;
            padding: 5px 15px;
            border: 1px solid #27ae60;
            display: inline-block;
        }

        .employee-info {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .employee-info table {
            width: 100%;
        }

        .employee-info td {
            padding: 3px 5px;
        }

        .employee-info td:first-child {
            font-weight: bold;
            width: 100px;
            color: #2c3e50;
        }

        .salary-section {
            margin-bottom: 15px;
        }

        .salary-section h4 {
            background: #3498db;
            color: #fff;
            padding: 5px 10px;
            margin-bottom: 0;
            font-size: 11px;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .salary-table td {
            border: 1px solid #bdc3c7;
            padding: 6px 10px;
            font-size: 10px;
        }

        .salary-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .salary-table .label {
            width: 60%;
            font-weight: 500;
        }

        .salary-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .salary-table .add {
            color: #27ae60;
        }

        .salary-table .deduct {
            color: #e74c3c;
        }

        .total-row {
            background: #2c3e50 !important;
            color: #fff;
        }

        .total-row td {
            font-weight: bold;
            font-size: 12px;
        }

        .payment-info {
            margin-bottom: 15px;
            padding: 10px;
            background: #e8f6f3;
            border-left: 4px solid #27ae60;
        }

        .payment-info table {
            width: 100%;
        }

        .payment-info td {
            padding: 3px 5px;
        }

        .footer {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding-top: 30px;
        }

        .signature-box .line {
            border-top: 1px solid #2c3e50;
            width: 100px;
            margin: 0 auto 5px;
        }

        .signature-box span {
            font-size: 10px;
            color: #2c3e50;
        }

        .note {
            margin-top: 15px;
            font-size: 9px;
            color: #95a5a6;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="payslip">
        <!-- Header -->
        <div class="header">
            <h1>{{ $institute['name'] }}</h1>
            <p>{{ $institute['address'] }}</p>
            @if($institute['phone'])
                <p>ফোন: {{ $institute['phone'] }} | ইমেইল: {{ $institute['email'] }}</p>
            @endif
            <div class="title">বেতন স্লিপ (Payslip)</div>
        </div>

        <!-- Employee Info -->
        <div class="employee-info">
            <table>
                <tr>
                    <td>নাম:</td>
                    <td><strong>{{ $employee->name ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td>পদবী:</td>
                    <td>{{ $employee->designation->name ?? ($payment->employee_type === 'teacher' ? 'শিক্ষক' : 'স্টাফ') }}
                    </td>
                </tr>
                <tr>
                    <td>আইডি:</td>
                    <td>{{ $employee->id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>মাস/সাল:</td>
                    <td><strong>{{ \App\Models\SalaryPayment::monthOptions()[$payment->month] ?? $payment->month }},
                            {{ $payment->year }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Salary Breakdown -->
        <div class="salary-section">
            <h4>বেতনের বিবরণ</h4>
            <table class="salary-table">
                <tr>
                    <td class="label">মূল বেতন (Basic Salary)</td>
                    <td class="amount add">৳ {{ number_format($payment->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">ভাতা (Allowances)</td>
                    <td class="amount add">৳ {{ number_format($payment->allowances, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">বোনাস (Bonus)</td>
                    <td class="amount add">৳ {{ number_format($payment->bonus, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">মোট যোগ (Gross)</td>
                    <td class="amount" style="border-top: 2px solid #2c3e50;">৳
                        {{ number_format($payment->basic_salary + $payment->allowances + $payment->bonus, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">কর্তন (Deductions)</td>
                    <td class="amount deduct">- ৳ {{ number_format($payment->deductions, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">অগ্রিম কর্তন (Advance)</td>
                    <td class="amount deduct">- ৳ {{ number_format($payment->advance_deduction, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">নিট বেতন (Net Salary)</td>
                    <td class="amount">৳ {{ number_format($payment->net_salary, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Info -->
        @if($payment->status === 'paid')
            <div class="payment-info">
                <table>
                    <tr>
                        <td><strong>স্ট্যাটাস:</strong></td>
                        <td style="color: #27ae60; font-weight: bold;">পরিশোধিত ✓</td>
                    </tr>
                    <tr>
                        <td><strong>পরিশোধের তারিখ:</strong></td>
                        <td>{{ $payment->payment_date?->format('d M Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>পদ্ধতি:</strong></td>
                        <td>{{ \App\Models\SalaryPayment::paymentMethodOptions()[$payment->payment_method] ?? $payment->payment_method }}
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <div class="payment-info" style="background: #fef9e7; border-color: #f1c40f;">
                <strong style="color: #d68910;">স্ট্যাটাস: বকেয়া</strong>
            </div>
        @endif

        <!-- Remarks -->
        @if($payment->remarks)
            <div style="padding: 10px; background: #f8f9fa; margin-bottom: 15px; font-size: 10px;">
                <strong>মন্তব্য:</strong> {{ $payment->remarks }}
            </div>
        @endif

        <!-- Signature -->
        <div class="footer">
            <div class="signature-box">
                <div class="line"></div>
                <span>প্রাপকের স্বাক্ষর</span>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <span>হিসাবরক্ষক/কর্তৃপক্ষ</span>
            </div>
        </div>

        <div class="note">
            এই স্লিপটি কম্পিউটার জেনারেটেড এবং স্বাক্ষর ছাড়াই বৈধ।<br>
            Generated on: {{ $generated_at }}
        </div>
    </div>
</body>

</html>