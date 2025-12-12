<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>হিফজ প্রগ্রেস রিপোর্ট</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        .container {
            padding: 15px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .institution-name {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
        }

        .report-title {
            font-size: 13px;
            color: #374151;
            margin-top: 3px;
        }

        .period {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }

        .stats-box {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            border: 1px solid #e5e7eb;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }

        .stat-label {
            font-size: 9px;
            color: #6b7280;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px 8px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        th {
            background: #ecfdf5;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #059669;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            margin-top: 20px;
        }

        .progress-bar {
            width: 50px;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            display: inline-block;
        }

        .progress-fill {
            height: 100%;
            background: #059669;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="institution-name">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
            <div class="report-title">হিফজ প্রগ্রেস রিপোর্ট</div>
            <div class="period">ক্লাস: {{ $className }} | সময়কাল: {{ $dateFrom }} - {{ $dateTo }}</div>
        </div>

        <div class="stats-box">
            <div class="stat-item">
                <div class="stat-value">{{ $summary['total_students'] }}</div>
                <div class="stat-label">মোট ছাত্র</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $summary['avg_para'] }}</div>
                <div class="stat-label">গড় পারা</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $summary['total_lines'] }}</div>
                <div class="stat-label">মোট লাইন</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $summary['completed_hifz'] }}</div>
                <div class="stat-label">হিফজ সম্পন্ন</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>রোল</th>
                    <th>নাম</th>
                    <th class="text-center">বর্তমান পারা</th>
                    <th class="text-center">সম্পন্ন পারা</th>
                    <th class="text-center">সাবাক দিন</th>
                    <th class="text-center">মোট লাইন</th>
                    <th class="text-center">গড় মান</th>
                    <th class="text-center">অগ্রগতি</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student['roll'] ?? '-' }}</td>
                        <td>{{ $student['name'] }}</td>
                        <td class="text-center">{{ $student['current_para'] }}</td>
                        <td class="text-center text-green">{{ $student['completed_paras'] }}/30</td>
                        <td class="text-center">{{ $student['total_sabaq_days'] }}</td>
                        <td class="text-center">{{ $student['total_lines'] }}</td>
                        <td class="text-center">{{ $student['avg_quality'] }}</td>
                        <td class="text-center">{{ $student['progress_percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">স্বয়ংক্রিয়ভাবে তৈরি | {{ $date }}</div>
    </div>
</body>

</html>