<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>কিতাব প্রগ্রেস রিপোর্ট</title>
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
            font-size: 13px;
            color: #374151;
            margin-top: 3px;
        }

        .class-name {
            font-size: 12px;
            font-weight: bold;
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
        }

        .stat-label {
            font-size: 9px;
            color: #6b7280;
        }

        h3 {
            font-size: 11px;
            margin: 10px 0 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 5px 8px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #059669;
        }

        .text-yellow {
            color: #ca8a04;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="institution-name">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
            <div class="report-title">কিতাব প্রগ্রেস রিপোর্ট</div>
            <div class="class-name">ক্লাস: {{ $className }}</div>
            <div style="font-size: 9px; color: #6b7280;">তারিখ: {{ $date }}</div>
        </div>

        <div class="stats-box">
            <div class="stat-item">
                <div class="stat-value">{{ $overallStats['total_students'] }}</div>
                <div class="stat-label">মোট ছাত্র</div>
            </div>
            <div class="stat-item">
                <div class="stat-value text-yellow">{{ $overallStats['active_progress'] }}</div>
                <div class="stat-label">চলমান</div>
            </div>
            <div class="stat-item">
                <div class="stat-value text-green">{{ $overallStats['completed'] }}</div>
                <div class="stat-label">সম্পন্ন</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $overallStats['total_pages'] }}</div>
                <div class="stat-label">মোট পৃষ্ঠা</div>
            </div>
        </div>

        @if(count($kitabSummary) > 0)
            <h3>কিতাব সারাংশ</h3>
            <table>
                <thead>
                    <tr>
                        <th>কিতাব</th>
                        <th class="text-center">অধ্যায়</th>
                        <th class="text-center">পাঠ</th>
                        <th class="text-center">সম্পন্ন</th>
                        <th class="text-center">গড় পৃষ্ঠা</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kitabSummary as $kitab)
                        <tr>
                            <td>{{ $kitab['name'] }}</td>
                            <td class="text-center">{{ $kitab['total_chapters'] }}</td>
                            <td class="text-center">{{ $kitab['total_lessons'] }}</td>
                            <td class="text-center text-green">{{ $kitab['completed_students'] }}</td>
                            <td class="text-center">{{ $kitab['avg_pages'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3>ছাত্র প্রগ্রেস</h3>
        <table>
            <thead>
                <tr>
                    <th>রোল</th>
                    <th>নাম</th>
                    <th class="text-center">অধ্যায়</th>
                    <th class="text-center">পাঠ</th>
                    <th class="text-center">পৃষ্ঠা</th>
                    <th class="text-center">শেষ পড়া</th>
                    <th class="text-center">স্ট্যাটাস</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student['roll'] ?? '-' }}</td>
                        <td>{{ $student['name'] }}</td>
                        <td class="text-center">{{ $student['total_chapters'] }}</td>
                        <td class="text-center">{{ $student['total_lessons'] }}</td>
                        <td class="text-center">{{ $student['total_pages'] }}</td>
                        <td class="text-center">{{ $student['last_date'] ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $statusLabel = match ($student['status']) {
                                    'completed' => 'সম্পন্ন',
                                    'in_progress' => 'চলমান',
                                    'revision' => 'রিভিশন',
                                    default => 'শুরু হয়নি',
                                };
                            @endphp
                            {{ $statusLabel }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">স্বয়ংক্রিয়ভাবে তৈরি | {{ institution_name() ?? 'মাদরাসা ম্যানেজমেন্ট সিস্টেম' }}</div>
    </div>
</body>

</html>