<!DOCTYPE html>
<html lang="bn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>মার্কশীট - {{ $student->name }}</title>
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
            line-height: 1.4;
            color: #2c3e50;
        }
        
        .container {
            border: 3px solid #1a5276;
            padding: 20px;
            position: relative;
        }
        
        /* Decorative border */
        .container::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 1px solid #85c1e9;
            pointer-events: none;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 22px;
            color: #1a5276;
            margin-bottom: 5px;
        }
        
        .header .address {
            font-size: 10px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .header .title {
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
            background: #d5f5e3;
            display: inline-block;
            padding: 8px 30px;
            border-radius: 20px;
        }
        
        .exam-title {
            text-align: center;
            font-size: 14px;
            color: #2980b9;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .student-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-col {
            display: table-cell;
            width: 50%;
            padding: 5px 10px;
        }
        
        .info-col strong {
            color: #7f8c8d;
            font-weight: normal;
            font-size: 10px;
        }
        
        .info-col span {
            display: block;
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 2px;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .marks-table th,
        .marks-table td {
            border: 1px solid #bdc3c7;
            padding: 10px 8px;
            text-align: center;
        }
        
        .marks-table th {
            background: linear-gradient(180deg, #3498db, #2980b9);
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }
        
        .marks-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .marks-table .subject-name {
            text-align: left;
            font-weight: 500;
        }
        
        .marks-table .marks {
            font-weight: bold;
            font-size: 12px;
        }
        
        .marks-table .pass {
            color: #27ae60;
        }
        
        .marks-table .fail {
            color: #e74c3c;
        }
        
        .marks-table .grade-cell {
            font-weight: bold;
        }
        
        .marks-table tfoot td {
            background: #2c3e50;
            color: #fff;
            font-weight: bold;
            font-size: 12px;
        }
        
        .result-box {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .result-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 2px solid #bdc3c7;
        }
        
        .result-item:first-child {
            border-right: none;
        }
        
        .result-item .value {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .result-item .label {
            font-size: 10px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .grade-a-plus { color: #27ae60 !important; }
        .grade-a { color: #2ecc71 !important; }
        .grade-b { color: #3498db !important; }
        .grade-c { color: #f39c12 !important; }
        .grade-d { color: #e67e22 !important; }
        .grade-f { color: #e74c3c !important; }
        
        .result-status {
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        
        .result-pass {
            background: #d5f5e3;
            border: 2px solid #27ae60;
        }
        
        .result-pass .status-text {
            color: #1e8449;
            font-size: 24px;
            font-weight: bold;
        }
        
        .result-fail {
            background: #fadbd8;
            border: 2px solid #e74c3c;
        }
        
        .result-fail .status-text {
            color: #922b21;
            font-size: 24px;
            font-weight: bold;
        }
        
        .grade-scale {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .grade-scale h4 {
            font-size: 10px;
            color: #7f8c8d;
            margin-bottom: 8px;
        }
        
        .grade-scale table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        
        .grade-scale th,
        .grade-scale td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
        }
        
        .grade-scale th {
            background: #ecf0f1;
        }
        
        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding-top: 30px;
        }
        
        .signature-box .line {
            border-top: 1px solid #2c3e50;
            width: 120px;
            margin: 0 auto 5px;
        }
        
        .signature-box span {
            font-size: 10px;
            color: #2c3e50;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #95a5a6;
            padding-top: 10px;
            border-top: 1px dashed #bdc3c7;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @php
                $logo = setting('site_logo');
            @endphp
            @if($logo && file_exists(public_path('storage/' . $logo)))
                <img src="{{ public_path('storage/' . $logo) }}" class="logo" alt="Logo">
            @endif
            <h1>{{ institution_name() }}</h1>
            <p class="address">{{ institution_address() }}</p>
            <div class="title">মার্কশীট (Mark Sheet)</div>
        </div>
        
        <!-- Exam Title -->
        <div class="exam-title">
            {{ $exam->name }} | {{ $exam->academicYear->name ?? '' }}
        </div>
        
        <!-- Student Info -->
        <div class="student-info">
            <div class="info-row">
                <div class="info-col">
                    <strong>ছাত্রের নাম</strong>
                    <span>{{ $student->name }}</span>
                </div>
                <div class="info-col">
                    <strong>পিতার নাম</strong>
                    <span>{{ $student->father_name }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-col">
                    <strong>ভর্তি নম্বর</strong>
                    <span>{{ $student->admission_no }}</span>
                </div>
                <div class="info-col">
                    <strong>রোল নম্বর</strong>
                    <span>{{ $student->roll_no ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-col">
                    <strong>শ্রেণি</strong>
                    <span>{{ $student->class->name ?? 'N/A' }}</span>
                </div>
                <div class="info-col">
                    <strong>শাখা</strong>
                    <span>{{ $student->section->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 30px;">ক্রম</th>
                    <th style="text-align: left;">বিষয়</th>
                    <th style="width: 60px;">পূর্ণ নম্বর</th>
                    <th style="width: 60px;">প্রাপ্ত নম্বর</th>
                    <th style="width: 60px;">পাস নম্বর</th>
                    <th style="width: 50px;">গ্রেড</th>
                    <th style="width: 40px;">GP</th>
                </tr>
            </thead>
            <tbody>
                @php $serial = 0; $allPassed = true; @endphp
                @foreach($marks as $mark)
                    @php 
                        $serial++; 
                        $isPassed = $mark->is_passed;
                        if (!$isPassed) $allPassed = false;
                    @endphp
                    <tr>
                        <td>{{ $serial }}</td>
                        <td class="subject-name">{{ $mark->subject->name ?? 'N/A' }}</td>
                        <td>{{ $mark->full_marks }}</td>
                        <td class="marks {{ $isPassed ? 'pass' : 'fail' }}">
                            {{ $mark->marks_obtained }}
                        </td>
                        <td>{{ $mark->pass_marks }}</td>
                        <td class="grade-cell grade-{{ strtolower(str_replace('+', '-plus', $mark->grade?->name ?? 'F')) }}">
                            {{ $mark->grade?->name ?? 'F' }}
                        </td>
                        <td>{{ $mark->grade?->grade_point ?? '0.00' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right;">মোট</td>
                    <td>{{ $totalFull }}</td>
                    <td>{{ $totalObtained }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Result Summary -->
        <div class="result-box">
            <div class="result-item">
                <div class="value">{{ $totalObtained }}/{{ $totalFull }}</div>
                <div class="label">মোট প্রাপ্ত নম্বর</div>
            </div>
            <div class="result-item">
                <div class="value">{{ $percentage }}%</div>
                <div class="label">শতাংশ</div>
            </div>
            <div class="result-item">
                <div class="value grade-{{ strtolower(str_replace('+', '-plus', $grade?->name ?? 'F')) }}">
                    {{ $grade?->name ?? 'F' }}
                </div>
                <div class="label">গ্রেড</div>
            </div>
            <div class="result-item">
                <div class="value">{{ $grade?->grade_point ?? '0.00' }}</div>
                <div class="label">GPA</div>
            </div>
        </div>
        
        <!-- Result Status -->
        <div class="result-status {{ $allPassed ? 'result-pass' : 'result-fail' }}">
            <span class="status-text">
                @if($allPassed)
                    ✓ পাস (PASSED)
                @else
                    ✗ ফেল (FAILED)
                @endif
            </span>
        </div>
        
        <!-- Grade Scale Reference -->
        <div class="grade-scale">
            <h4>গ্রেডিং স্কেল:</h4>
            <table>
                <tr>
                    <th>গ্রেড</th>
                    <th>A+</th>
                    <th>A</th>
                    <th>A-</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>F</th>
                </tr>
                <tr>
                    <td>নম্বর</td>
                    <td>80-100</td>
                    <td>70-79</td>
                    <td>60-69</td>
                    <td>50-59</td>
                    <td>40-49</td>
                    <td>33-39</td>
                    <td>0-32</td>
                </tr>
                <tr>
                    <td>GP</td>
                    <td>5.00</td>
                    <td>4.00</td>
                    <td>3.50</td>
                    <td>3.00</td>
                    <td>2.00</td>
                    <td>1.00</td>
                    <td>0.00</td>
                </tr>
            </table>
        </div>
        
        <!-- Signatures -->
        <div class="signatures">
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
        
        <!-- Footer -->
        <div class="footer">
            প্রকাশের তারিখ: {{ now()->format('d M Y') }} | 
            এই মার্কশীটটি প্রতিষ্ঠানের সিল ও স্বাক্ষর ছাড়া গ্রহণযোগ্য নয়।
        </div>
    </div>
</body>
</html>
