<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>রিপোর্ট কার্ড</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
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
        .report-card {
            page-break-after: always;
            padding: 15px;
            border: 3px double #2d5a27;
            margin-bottom: 10px;
        }
        .report-card:last-child {
            page-break-after: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2d5a27;
        }
        .header-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 5px;
        }
        .header h1 {
            font-size: 18pt;
            color: #2d5a27;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 9pt;
            color: #666;
        }
        .exam-title {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            background: linear-gradient(135deg, #2d5a27, #4a8c3f);
            color: white;
            font-size: 14pt;
            font-weight: bold;
            border-radius: 5px;
        }
        .student-info {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .student-photo {
            width: 80px;
            height: 90px;
            object-fit: cover;
            border: 2px solid #2d5a27;
            border-radius: 5px;
        }
        .student-photo-placeholder {
            width: 80px;
            height: 90px;
            background: #e9ecef;
            border: 2px solid #2d5a27;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24pt;
            color: #6c757d;
        }
        .student-details {
            flex: 1;
        }
        .student-details table {
            width: 100%;
        }
        .student-details td {
            padding: 3px 5px;
            font-size: 10pt;
        }
        .student-details td:first-child {
            font-weight: bold;
            width: 100px;
            color: #555;
        }
        .result-summary {
            text-align: right;
            padding: 10px;
        }
        .gpa-box {
            display: inline-block;
            padding: 8px 15px;
            background: #2d5a27;
            color: white;
            font-size: 16pt;
            font-weight: bold;
            border-radius: 8px;
            margin-bottom: 5px;
        }
        .gpa-box.failed {
            background: #dc3545;
        }
        .position-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #ffc107;
            color: #333;
            font-size: 10pt;
            font-weight: bold;
            border-radius: 15px;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }
        .marks-table th,
        .marks-table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: center;
        }
        .marks-table th {
            background: #2d5a27;
            color: white;
            font-weight: bold;
        }
        .marks-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .marks-table .subject-name {
            text-align: left;
            font-weight: 500;
        }
        .marks-table .passed { color: #28a745; }
        .marks-table .failed { color: #dc3545; }
        .marks-table .absent { 
            color: #dc3545; 
            font-style: italic;
        }
        .grade-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 10px;
            font-size: 9pt;
            font-weight: bold;
        }
        .summary-row {
            background: #343a40 !important;
            color: white !important;
            font-weight: bold;
        }
        .summary-row td {
            border-color: #343a40;
        }
        .attendance-box {
            margin-top: 15px;
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
        }
        .attendance-box h4 {
            font-size: 10pt;
            color: #856404;
            margin-bottom: 5px;
        }
        .attendance-stats {
            display: flex;
            gap: 20px;
            font-size: 9pt;
        }
        .attendance-stats span {
            padding: 3px 8px;
            border-radius: 3px;
        }
        .attendance-stats .present { background: #d4edda; color: #155724; }
        .attendance-stats .absent { background: #f8d7da; color: #721c24; }
        .attendance-stats .late { background: #fff3cd; color: #856404; }
        .attendance-stats .percentage { background: #cce5ff; color: #004085; font-weight: bold; }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 120px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 35px;
            padding-top: 5px;
            font-size: 9pt;
        }
        .remarks-box {
            margin-top: 15px;
            padding: 8px;
            background: #e7f5ff;
            border-left: 4px solid #1976d2;
            font-size: 9pt;
        }
        .grade-scale {
            margin-top: 15px;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            font-size: 8pt;
        }
        .grade-scale table {
            width: 100%;
        }
        .grade-scale td {
            padding: 2px 5px;
            text-align: center;
        }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
    </style>
</head>
<body>
    @foreach($reportData['cards'] as $card)
    <div class="report-card">
        {{-- Header --}}
        <div class="header">
            @if($institute['logo'])
            <img src="{{ public_path('storage/' . $institute['logo']) }}" class="header-logo" alt="Logo">
            @endif
            <h1>{{ $institute['name'] ?? 'প্রতিষ্ঠানের নাম' }}</h1>
            <p>{{ $institute['address'] ?? '' }}</p>
            @if($institute['phone'] || $institute['email'])
            <p>📞 {{ $institute['phone'] ?? '' }} | ✉ {{ $institute['email'] ?? '' }}</p>
            @endif
        </div>

        {{-- Exam Title --}}
        <div class="exam-title">
            {{ $reportData['exam']->name ?? 'পরীক্ষা' }} - {{ $reportData['exam']->academicYear?->name ?? '' }}
        </div>

        {{-- Student Info --}}
        <div class="student-info">
            @if($card['include_photo'] && $card['student']->photo)
            <img src="{{ public_path('storage/' . $card['student']->photo) }}" class="student-photo" alt="Photo">
            @else
            <div class="student-photo-placeholder">
                {{ mb_substr($card['student']->name, 0, 1) }}
            </div>
            @endif
            
            <div class="student-details">
                <table>
                    <tr>
                        <td>নাম:</td>
                        <td><strong>{{ $card['student']->name }}</strong></td>
                        <td>শ্রেণি:</td>
                        <td>{{ $card['student']->class?->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>রোল নং:</td>
                        <td>{{ $card['student']->roll_no ?? '-' }}</td>
                        <td>শাখা:</td>
                        <td>{{ $card['student']->section?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>আইডি:</td>
                        <td>{{ $card['student']->student_id ?? $card['student']->admission_no }}</td>
                        <td>পিতার নাম:</td>
                        <td>{{ $card['student']->father_name ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="result-summary">
                @if($card['result'])
                <div class="gpa-box {{ $card['result']->result_status !== 'passed' ? 'failed' : '' }}">
                    GPA {{ number_format($card['result']->gpa ?? 0, 2) }}
                </div>
                <br>
                <span class="position-badge">মেধা ক্রম: {{ $card['result']->position ?? '-' }}</span>
                @endif
            </div>
        </div>

        {{-- Marks Table --}}
        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 40px;">ক্রম</th>
                    <th class="subject-name">বিষয়</th>
                    <th style="width: 70px;">পূর্ণ নম্বর</th>
                    <th style="width: 70px;">প্রাপ্ত নম্বর</th>
                    <th style="width: 60px;">গ্রেড</th>
                    <th style="width: 60px;">ফলাফল</th>
                </tr>
            </thead>
            <tbody>
                @php $totalFull = 0; $totalObtained = 0; @endphp
                @foreach($card['subjects'] as $index => $subject)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="subject-name">{{ $subject['subject'] }}</td>
                    <td>{{ $subject['full_marks'] }}</td>
                    <td class="{{ $subject['is_absent'] ? 'absent' : '' }}">
                        {{ $subject['is_absent'] ? 'অনুপস্থিত' : $subject['obtained'] }}
                    </td>
                    <td><span class="grade-badge">{{ $subject['grade'] }}</span></td>
                    <td class="{{ $subject['is_passed'] ? 'passed' : 'failed' }}">
                        {{ $subject['is_absent'] ? '-' : ($subject['is_passed'] ? '✓' : '✗') }}
                    </td>
                </tr>
                @php 
                    $totalFull += $subject['full_marks']; 
                    $totalObtained += $subject['is_absent'] ? 0 : $subject['obtained']; 
                @endphp
                @endforeach
                <tr class="summary-row">
                    <td colspan="2">মোট</td>
                    <td>{{ $totalFull }}</td>
                    <td>{{ $totalObtained }}</td>
                    <td colspan="2">
                        {{ $card['result'] ? ($card['result']->result_status === 'passed' ? '✓ উত্তীর্ণ' : '✗ অনুত্তীর্ণ') : '-' }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Attendance --}}
        @if($card['attendance'])
        <div class="attendance-box">
            <h4>📅 উপস্থিতি সামারি (এই মাস)</h4>
            <div class="attendance-stats">
                <span class="present">উপস্থিত: {{ $card['attendance']['present'] }}</span>
                <span class="absent">অনুপস্থিত: {{ $card['attendance']['absent'] }}</span>
                <span class="late">বিলম্বে: {{ $card['attendance']['late'] }}</span>
                <span class="percentage">উপস্থিতি: {{ $card['attendance']['percentage'] }}%</span>
            </div>
        </div>
        @endif

        {{-- Grade Scale --}}
        <div class="grade-scale">
            <strong>গ্রেডিং স্কেল:</strong>
            <table>
                <tr>
                    <td>A+ (80-100)</td>
                    <td>A (70-79)</td>
                    <td>A- (60-69)</td>
                    <td>B (50-59)</td>
                    <td>C (40-49)</td>
                    <td>D (33-39)</td>
                    <td>F (0-32)</td>
                </tr>
            </table>
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">শ্রেণি শিক্ষক</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">অভিভাবক</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">প্রধান শিক্ষক</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            তৈরির তারিখ: {{ $reportData['generated_at']->format('d M Y, h:i A') }}
        </div>
    </div>
    @endforeach
</body>
</html>
