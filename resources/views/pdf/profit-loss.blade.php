<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>লাভ-ক্ষতি বিবরণী</title>
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

        .date-range {
            font-size: 11px;
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

        .section-title.income {
            background: #d1fae5;
            color: #065f46;
        }

        .section-title.expense {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 10px;
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

        .total-row {
            font-weight: bold;
            border-top: 2px solid #374151;
            background: #f9fafb;
        }

        .net-result {
            background: #1e40af;
            color: white;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }

        .net-result .amount {
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
            <div class="report-title">লাভ-ক্ষতি বিবরণী (Profit & Loss Statement)</div>
            <div class="date-range">{{ $dateFrom }} থেকে {{ $dateTo }} | প্রস্তুত: {{ $date }}</div>
        </div>

        {{-- Income Section --}}
        <div class="section">
            <div class="section-title income">আয় (Revenue/Income)</div>
            <table>
                <tr>
                    <td>ফি আদায় (Fee Collection)</td>
                    <td class="text-right text-green">৳{{ number_format($incomeData['fee_collection'], 0) }}</td>
                </tr>
                @foreach($incomeData['other_incomes'] as $income)
                    <tr>
                        <td>{{ $income['head'] }}</td>
                        <td class="text-right text-green">৳{{ number_format($income['amount'], 0) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>মোট আয়</strong></td>
                    <td class="text-right text-green"><strong>৳{{ number_format($incomeData['total'], 0) }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Expense Section --}}
        <div class="section">
            <div class="section-title expense">ব্যয় (Expenses)</div>
            <table>
                <tr>
                    <td>বেতন (Salary)</td>
                    <td class="text-right text-red">৳{{ number_format($expenseData['salary_expense'], 0) }}</td>
                </tr>
                @foreach($expenseData['other_expenses'] as $expense)
                    <tr>
                        <td>{{ $expense['head'] }}</td>
                        <td class="text-right text-red">৳{{ number_format($expense['amount'], 0) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>মোট ব্যয়</strong></td>
                    <td class="text-right text-red"><strong>৳{{ number_format($expenseData['total'], 0) }}</strong></td>
                </tr>
            </table>
        </div>

        {{-- Net Result --}}
        <div class="net-result" style="background: {{ $summary['is_profit'] ? '#059669' : '#dc2626' }}">
            <div>নেট {{ $summary['is_profit'] ? 'লাভ (Net Profit)' : 'ক্ষতি (Net Loss)' }}</div>
            <div class="amount">৳{{ number_format(abs($summary['net_profit']), 0) }}</div>
            <div style="font-size: 11px; margin-top: 5px;">{{ $summary['profit_margin'] }}%</div>
        </div>

        <div class="footer">
            স্বয়ংক্রিয়ভাবে তৈরি | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}
        </div>
    </div>
</body>

</html>