<!DOCTYPE html>
<html lang="bn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>হাজিরা রিপোর্ট</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Bengali', 'Kalpurush', sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #7f8c8d;
        }

        .header .report-title {
            margin-top: 10px;
            font-size: 14px;
            color: #27ae60;
            font-weight: bold;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            background: #ecf0f1;
            padding: 10px;
        }

        .info-row .info-item {
            display: table-cell;
            text-align: center;
        }

        .info-row .info-item strong {
            display: block;
            font-size: 11px;
            color: #2c3e50;
        }

        .info-row .info-item span {
            font-size: 10px;
            color: #7f8c8d;
        }

        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            border: 1px solid #bdc3c7;
        }

        .summary-item.green {
            background: #e8f6f3;
            border-color: #1abc9c;
        }

        .summary-item.blue {
            background: #ebf5fb;
            border-color: #3498db;
        }

        .summary-item.red {
            background: #fdedec;
            border-color: #e74c3c;
        }

        .summary-item.yellow {
            background: #fef9e7;
            border-color: #f1c40f;
        }

        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        .summary-item .label {
            font-size: 9px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #bdc3c7;
            padding: 6px 8px;
            text-align: center;
            font-size: 9px;
        }

        th {
            background: #3498db;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-left {
            text-align: left;
        }

        .text-green {
            color: #27ae60;
            font-weight: bold;
        }

        .text-red {
            color: #e74c3c;
            font-weight: bold;
        }

        .text-yellow {
            color: #f39c12;
        }

        .text-blue {
            color: #3498db;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
        }

        .badge-green {
            background: #d5f5e3;
            color: #1e8449;
        }

        .badge-blue {
            background: #d4e6f1;
            color: #1a5276;
        }

        .badge-yellow {
            background: #fef9e7;
            color: #9a7d0a;
        }

        .badge-red {
            background: #fadbd8;
            color: #922b21;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #95a5a6;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $institute['name'] }}</h1>
            <p>{{ $institute['address'] }}</p>
            <div class="report-title">হাজিরা রিপোর্ট (Attendance Report)</div>
        </div>

        <!-- Info Row -->
        <div class="info-row">
            <div class="info-item">
                <strong>{{ $reportData['class']->name ?? 'N/A' }}</strong>
                <span>শ্রেণি</span>
            </div>
            <div class="info-item">
                <strong>{{ $reportData['section'] }}</strong>
                <span>শাখা</span>
            </div>
            <div class="info-item">
                <strong>{{ \Carbon\Carbon::parse($reportData['start_date'])->format('d/m/Y') }}</strong>
                <span>শুরুর তারিখ</span>
            </div>
            <div class="info-item">
                <strong>{{ \Carbon\Carbon::parse($reportData['end_date'])->format('d/m/Y') }}</strong>
                <span>শেষের তারিখ</span>
            </div>
            <div class="info-item">
                <strong>{{ $reportData['total_days'] }} দিন</strong>
                <span>মোট দিন</span>
            </div>
        </div>

        <!-- Summary -->
        @php
            $students = collect($reportData['students']);
            $totalStudents = $students->count();
            $avgAttendance = round($students->avg('percentage'), 1);
            $above90 = $students->where('percentage', '>=', 90)->count();
            $below50 = $students->where('percentage', '<', 50)->count();
        @endphp
        <div class="summary-box">
            <div class="summary-item blue">
                <div class="value">{{ $totalStudents }}</div>
                <div class="label">মোট ছাত্র</div>
            </div>
            <div class="summary-item green">
                <div class="value">{{ $avgAttendance }}%</div>
                <div class="label">গড় হাজিরা</div>
            </div>
            <div class="summary-item green">
                <div class="value">{{ $above90 }}</div>
                <div class="label">৯০%+ হাজিরা</div>
            </div>
            <div class="summary-item red">
                <div class="value">{{ $below50 }}</div>
                <div class="label">৫০% এর নিচে</div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">ক্রম</th>
                    <th style="width: 40px;">রোল</th>
                    <th class="text-left" style="width: 150px;">নাম</th>
                    <th style="width: 50px;">উপস্থিত</th>
                    <th style="width: 50px;">অনুপস্থিত</th>
                    <th style="width: 50px;">বিলম্বে</th>
                    <th style="width: 50px;">ছুটি</th>
                    <th style="width: 50px;">মোট</th>
                    <th style="width: 60px;">শতাংশ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['students'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['student']->roll_no ?? '-' }}</td>
                                <td class="text-left">{{ $item['student']->name }}</td>
                                <td class="text-green">{{ $item['present'] }}</td>
                                <td class="text-red">{{ $item['absent'] }}</td>
                                <td class="text-yellow">{{ $item['late'] }}</td>
                                <td class="text-blue">{{ $item['leave'] }}</td>
                                <td>{{ $item['total'] }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $item['percentage'] >= 90 ? 'badge-green' :
                    ($item['percentage'] >= 75 ? 'badge-blue' :
                        ($item['percentage'] >= 50 ? 'badge-yellow' : 'badge-red')) }}">
                                        {{ $item['percentage'] }}%
                                    </span>
                                </td>
                            </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            Generated on: {{ $generated_at }} | {{ $institute['name'] }}
        </div>
    </div>
</body>

</html>