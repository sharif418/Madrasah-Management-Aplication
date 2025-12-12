<!DOCTYPE html>
<html lang="bn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ট্যাবুলেশন শীট - {{ $exam->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 15mm 10mm;
        }

        body {
            font-family: 'Noto Sans Bengali', 'Kalpurush', sans-serif;
            font-size: 9px;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            color: #1a5276;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
            color: #7f8c8d;
        }

        .header .title {
            margin-top: 8px;
            font-size: 14px;
            color: #27ae60;
            font-weight: bold;
        }

        .exam-info {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            background: #ecf0f1;
            padding: 8px;
        }

        .exam-info-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
        }

        .exam-info-item strong {
            font-size: 10px;
            color: #2c3e50;
        }

        .exam-info-item span {
            display: block;
            font-size: 8px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #bdc3c7;
            padding: 4px 3px;
            text-align: center;
            font-size: 8px;
        }

        th {
            background: #3498db;
            color: #fff;
            font-weight: bold;
            white-space: nowrap;
        }

        th.subject-header {
            background: #2980b9;
            min-width: 50px;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        tr:hover {
            background: #ebf5fb;
        }

        .student-name {
            text-align: left;
            font-weight: 500;
            white-space: nowrap;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .roll-no {
            font-weight: bold;
            color: #2c3e50;
        }

        .marks-cell {
            font-weight: 500;
        }

        .marks-pass {
            color: #27ae60;
        }

        .marks-fail {
            color: #e74c3c;
            font-weight: bold;
        }

        .total-cell {
            font-weight: bold;
            background: #f7dc6f !important;
        }

        .percentage-cell {
            font-weight: bold;
            color: #2c3e50;
        }

        .grade-cell {
            font-weight: bold;
        }

        .grade-a-plus {
            color: #27ae60;
        }

        .grade-a {
            color: #2ecc71;
        }

        .grade-b {
            color: #3498db;
        }

        .grade-c {
            color: #f39c12;
        }

        .grade-d {
            color: #e67e22;
        }

        .grade-f {
            color: #e74c3c;
        }

        .position-cell {
            font-weight: bold;
            color: #9b59b6;
        }

        .position-1 {
            color: #f39c12;
            font-size: 10px;
        }

        .position-2 {
            color: #7f8c8d;
            font-size: 9px;
        }

        .position-3 {
            color: #cd6155;
            font-size: 9px;
        }

        .status-pass {
            background: #d5f5e3 !important;
            color: #1e8449;
            font-weight: bold;
        }

        .status-fail {
            background: #fadbd8 !important;
            color: #922b21;
            font-weight: bold;
        }

        .summary-box {
            display: table;
            width: 100%;
            margin-top: 15px;
            border: 1px solid #bdc3c7;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            border-right: 1px solid #bdc3c7;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }

        .summary-item .label {
            font-size: 8px;
            color: #7f8c8d;
        }

        .footer {
            margin-top: 25px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding-top: 30px;
        }

        .signature-box .line {
            border-top: 1px solid #2c3e50;
            width: 100px;
            margin: 0 auto 5px;
        }

        .signature-box span {
            font-size: 9px;
            color: #2c3e50;
        }

        .print-footer {
            margin-top: 15px;
            font-size: 8px;
            color: #95a5a6;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ institution_name() }}</h1>
        <p>{{ institution_address() }}</p>
        <div class="title">ট্যাবুলেশন শীট (Tabulation Sheet)</div>
    </div>

    <!-- Exam Info -->
    <div class="exam-info">
        <div class="exam-info-item">
            <strong>{{ $exam->name }}</strong>
            <span>পরীক্ষার নাম</span>
        </div>
        <div class="exam-info-item">
            <strong>{{ $exam->class->name ?? 'N/A' }}</strong>
            <span>শ্রেণি</span>
        </div>
        <div class="exam-info-item">
            <strong>{{ $exam->academicYear->name ?? 'N/A' }}</strong>
            <span>শিক্ষাবর্ষ</span>
        </div>
        <div class="exam-info-item">
            <strong>{{ $exam->start_date?->format('d/m/Y') }} - {{ $exam->end_date?->format('d/m/Y') }}</strong>
            <span>পরীক্ষার তারিখ</span>
        </div>
        <div class="exam-info-item">
            <strong>{{ count($studentResults) }} জন</strong>
            <span>মোট পরীক্ষার্থী</span>
        </div>
    </div>

    <!-- Tabulation Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">ক্রম</th>
                <th style="width: 35px;">রোল</th>
                <th style="width: 100px; text-align: left;">ছাত্রের নাম</th>
                @foreach($subjects as $subject)
                    <th class="subject-header">
                        {{ \Illuminate\Support\Str::limit($subject->name, 8) }}
                        <br><small>({{ $subject->code ?? '' }})</small>
                    </th>
                @endforeach
                <th style="width: 40px;">মোট</th>
                <th style="width: 35px;">%</th>
                <th style="width: 35px;">গ্রেড</th>
                <th style="width: 30px;">GPA</th>
                <th style="width: 35px;">মেধা</th>
                <th style="width: 40px;">ফলাফল</th>
            </tr>
        </thead>
        <tbody>
            @php $serial = 0; @endphp
            @forelse($studentResults as $studentId => $data)
                @php $serial++; @endphp
                <tr>
                    <td>{{ $serial }}</td>
                    <td class="roll-no">{{ $data['student']->roll_no ?? '-' }}</td>
                    <td class="student-name">{{ $data['student']->name }}</td>

                    @foreach($subjects as $subject)
                        @php
                            $subjectMark = $data['subjects'][$subject->id] ?? null;
                            $isPassed = $subjectMark['passed'] ?? false;
                        @endphp
                        <td class="marks-cell {{ $isPassed ? 'marks-pass' : 'marks-fail' }}">
                            {{ $subjectMark['marks'] ?? '-' }}
                        </td>
                    @endforeach

                    <td class="total-cell">{{ $data['total_obtained'] }}/{{ $data['total_full'] }}</td>
                    <td class="percentage-cell">{{ number_format($data['percentage'], 1) }}%</td>
                    <td class="grade-cell grade-{{ strtolower(str_replace('+', '-plus', $data['grade'])) }}">
                        {{ $data['grade'] }}
                    </td>
                    <td>{{ number_format($data['gpa'], 2) }}</td>
                    <td class="position-cell position-{{ $data['position'] <= 3 ? $data['position'] : 'other' }}">
                        @if($data['position'] == 1)
                            🥇 {{ $data['position'] }}
                        @elseif($data['position'] == 2)
                            🥈 {{ $data['position'] }}
                        @elseif($data['position'] == 3)
                            🥉 {{ $data['position'] }}
                        @else
                            {{ $data['position'] }}
                        @endif
                    </td>
                    <td class="{{ $data['is_passed'] ? 'status-pass' : 'status-fail' }}">
                        {{ $data['is_passed'] ? 'পাস' : 'ফেল' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + count($subjects) }}" style="text-align: center; padding: 20px;">
                        কোন ফলাফল নেই
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    @php
        $totalStudents = count($studentResults);
        $passedCount = collect($studentResults)->where('is_passed', true)->count();
        $failedCount = $totalStudents - $passedCount;
        $passRate = $totalStudents > 0 ? round(($passedCount / $totalStudents) * 100, 1) : 0;
        $avgPercentage = $totalStudents > 0 ? round(collect($studentResults)->avg('percentage'), 1) : 0;
        $highestMarks = collect($studentResults)->max('percentage');
        $lowestMarks = collect($studentResults)->min('percentage');
    @endphp

    <div class="summary-box">
        <div class="summary-item">
            <div class="value">{{ $totalStudents }}</div>
            <div class="label">মোট পরীক্ষার্থী</div>
        </div>
        <div class="summary-item">
            <div class="value" style="color: #27ae60;">{{ $passedCount }}</div>
            <div class="label">পাস</div>
        </div>
        <div class="summary-item">
            <div class="value" style="color: #e74c3c;">{{ $failedCount }}</div>
            <div class="label">ফেল</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $passRate }}%</div>
            <div class="label">পাসের হার</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $avgPercentage }}%</div>
            <div class="label">গড় নম্বর</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $highestMarks }}%</div>
            <div class="label">সর্বোচ্চ</div>
        </div>
        <div class="summary-item">
            <div class="value">{{ $lowestMarks }}%</div>
            <div class="label">সর্বনিম্ন</div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="footer">
        <div class="signature-box">
            <div class="line"></div>
            <span>শ্রেণি শিক্ষক</span>
        </div>
        <div class="signature-box">
            <div class="line"></div>
            <span>পরীক্ষা সমন্বয়কারী</span>
        </div>
        <div class="signature-box">
            <div class="line"></div>
            <span>প্রধান শিক্ষক/অধ্যক্ষ</span>
        </div>
    </div>

    <div class="print-footer">
        Generated on: {{ now()->format('d M Y, h:i A') }} | {{ institution_name() }}
    </div>
</body>

</html>