<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>অগ্রগতি প্রতিবেদন</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 16pt;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 9pt;
            color: #666;
        }

        .report-title {
            text-align: center;
            margin: 15px 0;
            font-size: 14pt;
            font-weight: bold;
            background: #2d5a27;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .student-info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .student-info table {
            width: 100%;
        }

        .student-info td {
            padding: 5px 10px;
        }

        .student-info td:first-child {
            width: 25%;
            color: #666;
        }

        .student-info td:last-child {
            font-weight: bold;
        }

        .summary-box {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background: #e8f4e8;
            border-radius: 8px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .value {
            font-size: 20pt;
            font-weight: bold;
            color: #2d5a27;
        }

        .summary-item .label {
            font-size: 9pt;
            color: #666;
        }

        .trend-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: bold;
        }

        .trend-badge.improving {
            background: #d4edda;
            color: #155724;
        }

        .trend-badge.declining {
            background: #f8d7da;
            color: #721c24;
        }

        .trend-badge.stable {
            background: #e2e3e5;
            color: #383d41;
        }

        .exam-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .exam-table th,
        .exam-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .exam-table th {
            background: #2d5a27;
            color: white;
        }

        .exam-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .passed {
            color: #28a745;
            font-weight: bold;
        }

        .failed {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
            text-align: center;
            font-size: 8pt;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $institute['name'] ?? 'প্রতিষ্ঠানের নাম' }}</h1>
        <p>{{ $institute['address'] ?? '' }}</p>
    </div>

    <div class="report-title">
        বার্ষিক অগ্রগতি প্রতিবেদন - {{ $progressData['academic_year']->name ?? '' }}
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td>ছাত্রের নাম:</td>
                <td>{{ $progressData['student']->name }}</td>
                <td>শ্রেণি:</td>
                <td>{{ $progressData['student']->class?->name ?? '' }}</td>
            </tr>
            <tr>
                <td>পিতার নাম:</td>
                <td>{{ $progressData['student']->father_name ?? '-' }}</td>
                <td>রোল নং:</td>
                <td>{{ $progressData['student']->roll_no ?? '-' }}</td>
            </tr>
            <tr>
                <td>আইডি:</td>
                <td>{{ $progressData['student']->student_id ?? $progressData['student']->admission_no }}</td>
                <td>শাখা:</td>
                <td>{{ $progressData['student']->section?->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="value">{{ $progressData['summary']['total_exams'] }}</div>
            <div class="label">মোট পরীক্ষা</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $progressData['summary']['passed_count'] }}</div>
            <div class="label">উত্তীর্ণ</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $progressData['summary']['avg_gpa'] }}</div>
            <div class="label">গড় GPA</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $progressData['summary']['avg_percentage'] }}%</div>
            <div class="label">গড় শতাংশ</div>
        </div>
        <div class="summary-item">
            <span class="trend-badge {{ $progressData['summary']['trend'] }}">
                @if($progressData['summary']['trend'] === 'improving') ↑ উন্নতি
                @elseif($progressData['summary']['trend'] === 'declining') ↓ অবনতি
                @else → স্থিতিশীল
                @endif
            </span>
        </div>
    </div>

    <h3 style="margin-top: 30px; font-size: 12pt; border-bottom: 1px solid #2d5a27; padding-bottom: 5px;">পরীক্ষাভিত্তিক
        ফলাফল</h3>

    <table class="exam-table">
        <thead>
            <tr>
                <th>ক্রম</th>
                <th>পরীক্ষার নাম</th>
                <th>প্রাপ্ত নম্বর</th>
                <th>পূর্ণ নম্বর</th>
                <th>শতাংশ</th>
                <th>GPA</th>
                <th>মেধা ক্রম</th>
                <th>ফলাফল</th>
            </tr>
        </thead>
        <tbody>
            @foreach($progressData['exams'] as $index => $examData)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left; font-weight: bold;">{{ $examData['exam']->name }}</td>
                    <td>{{ $examData['total_marks'] }}</td>
                    <td>{{ $examData['full_marks'] }}</td>
                    <td>{{ number_format($examData['percentage'], 1) }}%</td>
                    <td>{{ number_format($examData['gpa'], 2) }}</td>
                    <td>{{ $examData['position'] }}</td>
                    <td class="{{ $examData['result_status'] === 'passed' ? 'passed' : 'failed' }}">
                        {{ $examData['result_status'] === 'passed' ? '✓ উত্তীর্ণ' : ($examData['result_status'] === 'failed' ? '✗ অনুত্তীর্ণ' : '-') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        তৈরির তারিখ: {{ $progressData['generated_at']->format('d M Y, h:i A') }}
    </div>
</body>

</html>