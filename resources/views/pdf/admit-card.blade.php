<!DOCTYPE html>
<html lang="bn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>প্রবেশপত্র - {{ $exam->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Bengali', 'Kalpurush', sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        .admit-card {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border: 3px solid #1a5276;
            padding: 20px;
            page-break-after: always;
            position: relative;
        }

        .admit-card:last-child {
            page-break-after: avoid;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1a5276;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header .logo {
            width: 70px;
            height: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            color: #1a5276;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            color: #27ae60;
            margin-bottom: 5px;
            border: 2px solid #27ae60;
            display: inline-block;
            padding: 5px 20px;
        }

        .header h3 {
            font-size: 14px;
            color: #2c3e50;
        }

        .header p {
            font-size: 11px;
            color: #7f8c8d;
        }

        .student-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .student-info {
            display: table-cell;
            width: 70%;
            vertical-align: top;
        }

        .student-photo {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        .student-photo .photo-box {
            width: 100px;
            height: 120px;
            border: 2px solid #1a5276;
            display: inline-block;
            text-align: center;
            line-height: 120px;
            color: #95a5a6;
            font-size: 10px;
            background: #ecf0f1;
        }

        .student-info table {
            width: 100%;
        }

        .student-info td {
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }

        .student-info td:first-child {
            font-weight: bold;
            width: 100px;
            color: #2c3e50;
        }

        .schedule-section {
            margin-bottom: 20px;
        }

        .schedule-section h4 {
            background: #1a5276;
            color: #fff;
            padding: 8px 15px;
            margin-bottom: 0;
            font-size: 13px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #bdc3c7;
            padding: 8px 10px;
            text-align: center;
            font-size: 11px;
        }

        .schedule-table th {
            background: #3498db;
            color: #fff;
            font-weight: bold;
        }

        .schedule-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .instructions {
            margin-bottom: 20px;
            padding: 10px;
            background: #fef9e7;
            border-left: 4px solid #f1c40f;
        }

        .instructions h4 {
            font-size: 12px;
            color: #d68910;
            margin-bottom: 8px;
        }

        .instructions ul {
            margin-left: 20px;
            font-size: 10px;
            color: #7b7d7d;
        }

        .instructions li {
            margin-bottom: 3px;
        }

        .footer {
            margin-top: 40px;
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
            width: 120px;
            margin: 0 auto 5px;
        }

        .signature-box span {
            font-size: 11px;
            color: #2c3e50;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.03);
            font-weight: bold;
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>

<body>
    @foreach($students as $student)
        <div class="admit-card">
            <div class="watermark">প্রবেশপত্র</div>

            <!-- Header -->
            <div class="header">
                @if(!empty($institute['logo']))
                    <img src="{{ public_path('storage/' . str_replace('storage/', '', $institute['logo'])) }}" alt="Logo"
                        class="logo">
                @endif
                <h1>{{ $institute['name'] }}</h1>
                <p>{{ $institute['address'] }}</p>
                <h2>প্রবেশপত্র (Admit Card)</h2>
                <h3>{{ $exam->name }} - {{ $exam->academicYear->name ?? '' }}</h3>
            </div>

            <!-- Student Info -->
            <div class="student-section">
                <div class="student-info">
                    <table>
                        <tr>
                            <td>নাম:</td>
                            <td><strong>{{ $student->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>ভর্তি নং:</td>
                            <td>{{ $student->admission_no }}</td>
                        </tr>
                        <tr>
                            <td>শ্রেণি:</td>
                            <td>{{ $student->class->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>শাখা:</td>
                            <td>{{ $student->section->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>রোল নং:</td>
                            <td><strong>{{ $student->roll_no ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td>পিতার নাম:</td>
                            <td>{{ $student->father_name }}</td>
                        </tr>
                    </table>
                </div>
                <div class="student-photo">
                    @php
                        $photoPath = $student->getFirstMediaPath('photo');
                    @endphp
                    @if($photoPath && file_exists($photoPath))
                        <img src="{{ $photoPath }}" class="photo-box" style="object-fit: cover;">
                    @else
                        <div class="photo-box">ছবি</div>
                    @endif
                </div>
            </div>

            <!-- Exam Schedule -->
            <div class="schedule-section">
                <h4>পরীক্ষার সময়সূচী</h4>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>ক্রম</th>
                            <th>তারিখ</th>
                            <th>বার</th>
                            <th>সময়</th>
                            <th>বিষয়</th>
                            <th>পূর্ণমান</th>
                            <th>রুম</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $index => $schedule)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $schedule->exam_date->format('d/m/Y') }}</td>
                                <td>{{ $schedule->exam_date->locale('bn')->dayName }}</td>
                                <td>{{ date('h:i A', strtotime($schedule->start_time)) }}</td>
                                <td>{{ $schedule->subject->name ?? 'N/A' }}</td>
                                <td>{{ $schedule->full_marks }}</td>
                                <td>{{ $schedule->room ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h4>নির্দেশাবলী:</h4>
                <ul>
                    <li>পরীক্ষা শুরু হওয়ার অন্তত ১৫ মিনিট পূর্বে পরীক্ষা হলে উপস্থিত হতে হবে।</li>
                    <li>প্রবেশপত্র ব্যতীত পরীক্ষায় অংশগ্রহণ করা যাবে না।</li>
                    <li>মোবাইল ফোন বা ইলেকট্রনিক ডিভাইস বহন সম্পূর্ণ নিষিদ্ধ।</li>
                    <li>নিজ আসনে শান্তশিষ্টভাবে বসে পরীক্ষা দিতে হবে।</li>
                </ul>
            </div>

            <!-- Signature -->
            <div class="footer">
                <div class="signature-box">
                    <div class="line"></div>
                    <span>শ্রেণি শিক্ষক</span>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <span>পরীক্ষা নিয়ন্ত্রক</span>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <span>অধ্যক্ষ/মুহতামিম</span>
                </div>
            </div>
        </div>
    @endforeach

    <div style="text-align: center; font-size: 9px; color: #95a5a6; margin-top: 10px;">
        Generated on: {{ $generated_at }}
    </div>
</body>

</html>