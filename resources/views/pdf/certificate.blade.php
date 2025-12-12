<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>{{ $certificateData['certificate_type_name'] }}</title>
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
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
        }

        .certificate {
            border: 8px double #2d5a27;
            padding: 30px;
            min-height: 700px;
            position: relative;
        }

        .corner-ornament {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 3px solid #2d5a27;
        }

        .corner-ornament.top-left {
            top: 10px;
            left: 10px;
            border-right: none;
            border-bottom: none;
        }

        .corner-ornament.top-right {
            top: 10px;
            right: 10px;
            border-left: none;
            border-bottom: none;
        }

        .corner-ornament.bottom-left {
            bottom: 10px;
            left: 10px;
            border-right: none;
            border-top: none;
        }

        .corner-ornament.bottom-right {
            bottom: 10px;
            right: 10px;
            border-left: none;
            border-top: none;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2d5a27;
        }

        .header img {
            width: 70px;
            height: 70px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 22pt;
            color: #2d5a27;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #666;
        }

        .certificate-title {
            text-align: center;
            margin: 30px 0;
        }

        .certificate-title h2 {
            font-size: 28pt;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .certificate-title .underline {
            width: 150px;
            height: 4px;
            background: linear-gradient(90deg, transparent, #b8860b, transparent);
            margin: 0 auto;
        }

        .certificate-no {
            text-align: right;
            font-size: 10pt;
            color: #666;
            margin-bottom: 20px;
        }

        .student-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .student-info table {
            width: 100%;
        }

        .student-info td {
            padding: 5px 10px;
            font-size: 11pt;
        }

        .student-info td:first-child {
            width: 25%;
            color: #666;
            font-weight: normal;
        }

        .student-info td:last-child {
            font-weight: bold;
            color: #333;
        }

        .content {
            text-align: justify;
            font-size: 13pt;
            line-height: 2;
            margin: 25px 20px;
            color: #444;
        }

        .extra-text {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin: 20px 0;
            font-size: 11pt;
            color: #856404;
        }

        .date-section {
            margin: 30px 0;
            font-size: 11pt;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
        }

        .signature-box {
            text-align: center;
            width: 150px;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 8px;
            margin-top: 60px;
            font-size: 10pt;
        }

        .seal-area {
            position: absolute;
            bottom: 100px;
            right: 80px;
            width: 80px;
            height: 80px;
            border: 2px dashed #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #999;
        }

        .footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="certificate">
        {{-- Corner Ornaments --}}
        <div class="corner-ornament top-left"></div>
        <div class="corner-ornament top-right"></div>
        <div class="corner-ornament bottom-left"></div>
        <div class="corner-ornament bottom-right"></div>

        {{-- Header --}}
        <div class="header">
            @if($institute['logo'])
                <img src="{{ public_path('storage/' . $institute['logo']) }}" alt="Logo">
            @endif
            <h1>{{ $institute['name'] ?? 'প্রতিষ্ঠানের নাম' }}</h1>
            <p>{{ $institute['address'] ?? '' }}</p>
            @if($institute['phone'])
                <p>📞 {{ $institute['phone'] }}</p>
            @endif
        </div>

        {{-- Certificate Title --}}
        <div class="certificate-title">
            <h2>{{ $certificateData['certificate_type_name'] }}</h2>
            <div class="underline"></div>
        </div>

        {{-- Certificate Number --}}
        <div class="certificate-no">
            সার্টিফিকেট নং: <strong>{{ $certificateData['certificate_no'] }}</strong>
        </div>

        {{-- Student Info --}}
        <div class="student-info">
            <table>
                <tr>
                    <td>নাম:</td>
                    <td>{{ $certificateData['student']->name }}</td>
                </tr>
                <tr>
                    <td>পিতার নাম:</td>
                    <td>{{ $certificateData['student']->father_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>শ্রেণি:</td>
                    <td>{{ $certificateData['student']->class?->name ?? '-' }}
                        {{ $certificateData['student']->section ? '(' . $certificateData['student']->section->name . ')' : '' }}
                    </td>
                </tr>
                <tr>
                    <td>আইডি নং:</td>
                    <td>{{ $certificateData['student']->student_id ?? $certificateData['student']->admission_no }}</td>
                </tr>
            </table>
        </div>

        {{-- Content --}}
        <div class="content">
            {!! nl2br(e($certificateData['content'])) !!}
        </div>

        {{-- Extra Text --}}
        @if($certificateData['extra_text'])
            <div class="extra-text">
                {!! nl2br(e($certificateData['extra_text'])) !!}
            </div>
        @endif

        {{-- Date --}}
        <div class="date-section">
            <strong>ইস্যু তারিখ:</strong> {{ $certificateData['issue_date']->format('d M, Y') }}
        </div>

        {{-- Seal Area --}}
        <div class="seal-area">সিল</div>

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
            তৈরির তারিখ: {{ $certificateData['generated_at']->format('d M Y, h:i A') }}
        </div>
    </div>
</body>

</html>