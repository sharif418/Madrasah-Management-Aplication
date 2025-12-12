<!DOCTYPE html>
<html lang="bn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>প্রাক্তন ছাত্র তালিকা</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 15mm;
        }

        body {
            font-family: 'Noto Sans Bengali', 'Kalpurush', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2c3e50;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #27ae60;
        }

        .header h1 {
            font-size: 24px;
            color: #27ae60;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 18px;
            color: #2c3e50;
            font-weight: bold;
        }

        .header .info {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 10px;
        }

        .year-badge {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }

        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #27ae60;
        }

        .stat-label {
            font-size: 11px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px 8px;
            text-align: left;
        }

        th {
            background: linear-gradient(180deg, #27ae60, #1e8449);
            color: white;
            font-weight: bold;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody tr:hover {
            background: #e8f8f5;
        }

        .serial {
            text-align: center;
            width: 40px;
        }

        .year {
            text-align: center;
            font-weight: bold;
            color: #3498db;
        }

        .occupation {
            font-style: italic;
            color: #27ae60;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #95a5a6;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
        }

        .year-section {
            margin-bottom: 25px;
        }

        .year-header {
            background: #2c3e50;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ institution_name() }}</h1>
        <p class="subtitle">প্রাক্তন ছাত্র তালিকা</p>
        <p class="info">{{ institution_address() }}</p>
        <div class="year-badge">
            @if($year === 'all')
                সকল বছর
            @else
                {{ $year }} সাল
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-item">
            <div class="stat-value">{{ $alumni->count() }}</div>
            <div class="stat-label">মোট প্রাক্তন ছাত্র</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $alumni->unique('passing_year')->count() }}</div>
            <div class="stat-label">বছর সংখ্যা</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                {{ $alumni->whereNotNull('current_occupation')->where('current_occupation', '!=', '')->count() }}</div>
            <div class="stat-label">পেশাগত তথ্য আছে</div>
        </div>
    </div>

    @if($alumni->count() > 0)
        @if($year === 'all')
            @foreach($alumni->groupBy('passing_year')->sortKeysDesc() as $passYear => $yearAlumni)
                <div class="year-section">
                    <div class="year-header">{{ $passYear }} সাল ({{ $yearAlumni->count() }} জন)</div>
                    <table>
                        <thead>
                            <tr>
                                <th class="serial">ক্রম</th>
                                <th>নাম</th>
                                <th>সর্বশেষ শ্রেণি</th>
                                <th>বর্তমান পেশা</th>
                                <th>যোগাযোগ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yearAlumni as $index => $alumnus)
                                <tr>
                                    <td class="serial">{{ $index + 1 }}</td>
                                    <td><strong>{{ $alumnus->name }}</strong></td>
                                    <td>{{ $alumnus->last_class ?? '-' }}</td>
                                    <td class="occupation">{{ $alumnus->current_occupation ?? '-' }}</td>
                                    <td>{{ $alumnus->phone ?? $alumnus->email ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <table>
                <thead>
                    <tr>
                        <th class="serial">ক্রম</th>
                        <th>নাম</th>
                        <th>সর্বশেষ শ্রেণি</th>
                        <th>বর্তমান পেশা</th>
                        <th>যোগাযোগ</th>
                        <th>ঠিকানা</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumni as $index => $alumnus)
                        <tr>
                            <td class="serial">{{ $index + 1 }}</td>
                            <td><strong>{{ $alumnus->name }}</strong></td>
                            <td>{{ $alumnus->last_class ?? '-' }}</td>
                            <td class="occupation">{{ $alumnus->current_occupation ?? '-' }}</td>
                            <td>{{ $alumnus->phone ?? $alumnus->email ?? '-' }}</td>
                            <td>{{ Str::limit($alumnus->current_address ?? '-', 40) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <div class="no-data">
            কোন প্রাক্তন ছাত্রের তথ্য পাওয়া যায়নি
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        প্রকাশের তারিখ: {{ now()->format('d M Y, h:i A') }} |
        এই তালিকাটি {{ institution_name() }} কর্তৃক স্বয়ংক্রিয়ভাবে তৈরি
    </div>
</body>

</html>