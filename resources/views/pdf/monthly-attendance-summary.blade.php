<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>মাসিক উপস্থিতি সামারি</title>
    <style>
        @page {
            size: A4 landscape;
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
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #666;
        }

        .report-title {
            text-align: center;
            margin: 15px 0;
            font-size: 14pt;
            font-weight: bold;
            background: #f5f5f5;
            padding: 8px;
            border-radius: 4px;
        }

        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .info-item {
            padding: 5px 10px;
            background: #e8f4ff;
            border-radius: 4px;
        }

        .stats-row {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .stat-box {
            padding: 8px 15px;
            border-radius: 5px;
            text-align: center;
            min-width: 80px;
        }

        .stat-box.present {
            background: #d4edda;
            color: #155724;
        }

        .stat-box.absent {
            background: #f8d7da;
            color: #721c24;
        }

        .stat-box.late {
            background: #fff3cd;
            color: #856404;
        }

        .stat-box.leave {
            background: #d1ecf1;
            color: #0c5460;
        }

        .stat-box .value {
            font-size: 18pt;
            font-weight: bold;
        }

        .stat-box .label {
            font-size: 9pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
        }

        th {
            background: #4a5568;
            color: white;
            font-weight: bold;
        }

        th.name-col {
            text-align: left;
            min-width: 120px;
        }

        td.name-col {
            text-align: left;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f0f0f0;
        }

        .status-present {
            color: #28a745;
            font-weight: bold;
        }

        .status-absent {
            color: #dc3545;
            font-weight: bold;
        }

        .status-late {
            color: #ffc107;
            font-weight: bold;
        }

        .status-leave {
            color: #17a2b8;
        }

        .percentage-high {
            background: #d4edda;
            color: #155724;
        }

        .percentage-mid {
            background: #fff3cd;
            color: #856404;
        }

        .percentage-low {
            background: #f8d7da;
            color: #721c24;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #666;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 150px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $institute['name'] ?? 'প্রতিষ্ঠানের নাম' }}</h1>
        <p>{{ $institute['address'] ?? '' }}</p>
    </div>

    {{-- Report Title --}}
    <div class="report-title">
        মাসিক উপস্থিতি সামারি - {{ $reportData['month_name'] }} {{ $reportData['year'] }}
    </div>

    {{-- Class Info --}}
    <div class="info-box">
        <span class="info-item"><strong>শ্রেণি:</strong> {{ $reportData['class']->name ?? '' }}</span>
        <span class="info-item"><strong>শাখা:</strong> {{ $reportData['section'] }}</span>
        <span class="info-item"><strong>কার্যদিবস:</strong> {{ $reportData['total_working_days'] }} দিন</span>
        <span class="info-item"><strong>মোট ছাত্র:</strong> {{ count($reportData['students']) }} জন</span>
    </div>

    {{-- Summary Stats --}}
    <div class="stats-row">
        <div class="stat-box present">
            <div class="value">{{ $reportData['overall_stats']['percentage'] }}%</div>
            <div class="label">গড় উপস্থিতি</div>
        </div>
        <div class="stat-box present">
            <div class="value">{{ $reportData['overall_stats']['total_present'] }}</div>
            <div class="label">মোট উপস্থিতি</div>
        </div>
        <div class="stat-box absent">
            <div class="value">{{ $reportData['overall_stats']['total_absent'] }}</div>
            <div class="label">মোট অনুপস্থিতি</div>
        </div>
        <div class="stat-box late">
            <div class="value">{{ $reportData['overall_stats']['total_late'] }}</div>
            <div class="label">মোট বিলম্বে</div>
        </div>
        <div class="stat-box leave">
            <div class="value">{{ $reportData['overall_stats']['total_leave'] }}</div>
            <div class="label">মোট ছুটি</div>
        </div>
    </div>

    {{-- Student Table --}}
    <table>
        <thead>
            <tr>
                <th>ক্রম</th>
                <th class="name-col">ছাত্রের নাম</th>
                <th>রোল</th>
                <th class="status-present">উপ.</th>
                <th class="status-absent">অনু.</th>
                <th class="status-late">বি.</th>
                <th class="status-leave">ছু.</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['students'] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="name-col">{{ $item['student']->name }}</td>
                    <td>{{ $item['student']->roll_no ?? '-' }}</td>
                    <td class="status-present">{{ $item['present'] }}</td>
                    <td class="status-absent">{{ $item['absent'] }}</td>
                    <td class="status-late">{{ $item['late'] }}</td>
                    <td class="status-leave">{{ $item['leave'] }}</td>
                    <td
                        class="@if($item['percentage'] >= 80) percentage-high @elseif($item['percentage'] >= 50) percentage-mid @else percentage-low @endif">
                        {{ $item['percentage'] }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">শ্রেণি শিক্ষক</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">প্রধান শিক্ষক</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">অধ্যক্ষ</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <span>তৈরির তারিখ: {{ $generated_at }}</span>
        <span>পৃষ্ঠা ১</span>
    </div>
</body>

</html>