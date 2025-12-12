<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>ছাড়পত্র/টিসি - {{ $student->admission_no }}</title>
    <style>
        @page {
            margin: 40px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Hind Siliguri', 'Noto Sans Bengali', serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #1f2937;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            border: 3px double #059669;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #059669;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30pt;
        }

        .institution-name {
            font-size: 22pt;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
            margin-top: 15px;
            text-decoration: underline;
        }

        .tc-number {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 10pt;
            color: #6b7280;
        }

        .date {
            position: absolute;
            top: 35px;
            right: 30px;
            font-size: 10pt;
            color: #6b7280;
        }

        .content {
            margin: 30px 0;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #d1d5db;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table tr {
            border-bottom: 1px dotted #e5e7eb;
        }

        .info-table td {
            padding: 8px 5px;
            vertical-align: top;
        }

        .info-table .label {
            width: 40%;
            font-weight: 600;
            color: #374151;
        }

        .info-table .value {
            color: #1f2937;
        }

        .certificate-text {
            text-align: justify;
            margin: 30px 0;
            font-size: 11pt;
            line-height: 2;
        }

        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            margin-bottom: 5px;
            padding-top: 5px;
        }

        .signature-title {
            font-size: 10pt;
            color: #6b7280;
        }

        .stamp-area {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            border: 1px dashed #d1d5db;
            color: #9ca3af;
            font-size: 10pt;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #9ca3af;
            text-align: center;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 60pt;
            color: rgba(5, 150, 105, 0.05);
            font-weight: bold;
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="watermark">ছাড়পত্র</div>

        <div class="tc-number">নং: TC-{{ date('Y') }}-{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</div>
        <div class="date">তারিখ: {{ now()->format('d/m/Y') }}</div>

        <div class="header">
            <div class="logo">🕌</div>
            <div class="institution-name">মাদরাসা ম্যানেজমেন্ট</div>
            <div style="font-size: 10pt; color: #6b7280;">ঠিকানা লাইন এখানে</div>
            <div class="document-title">ছাড়পত্র / Transfer Certificate</div>
        </div>

        <div class="content">
            <div class="section">
                <div class="section-title">ছাত্রের ব্যক্তিগত তথ্য</div>
                <table class="info-table">
                    <tr>
                        <td class="label">ছাত্রের নাম:</td>
                        <td class="value">{{ $student->name }} @if($student->name_en)({{ $student->name_en }})@endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label">পিতার নাম:</td>
                        <td class="value">{{ $student->father_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">মাতার নাম:</td>
                        <td class="value">{{ $student->mother_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">জন্ম তারিখ:</td>
                        <td class="value">{{ $student->date_of_birth?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">ঠিকানা:</td>
                        <td class="value">{{ $student->permanent_address ?? $student->present_address ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">একাডেমিক তথ্য</div>
                <table class="info-table">
                    <tr>
                        <td class="label">ভর্তি নম্বর:</td>
                        <td class="value">{{ $student->admission_no }}</td>
                    </tr>
                    <tr>
                        <td class="label">ভর্তির তারিখ:</td>
                        <td class="value">{{ $student->admission_date?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">সর্বশেষ শ্রেণি:</td>
                        <td class="value">{{ $student->class?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">শাখা:</td>
                        <td class="value">{{ $student->section?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">শিক্ষাবর্ষ:</td>
                        <td class="value">{{ $student->academicYear?->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="certificate-text">
                এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, <strong>{{ $student->name }}</strong>, পিতা-
                <strong>{{ $student->father_name }}</strong>,
                আমাদের এই প্রতিষ্ঠানে {{ $student->admission_date?->format('d/m/Y') ?? 'N/A' }} তারিখে ভর্তি হয়ে
                {{ $student->class?->name ?? 'N/A' }} শ্রেণি পর্যন্ত অধ্যয়ন করেছে।
                তার আচার-আচরণ সন্তোষজনক ছিল এবং এই প্রতিষ্ঠানের বিরুদ্ধে তার কোন পাওনা নেই।
                তার ভবিষ্যৎ জীবনে সাফল্য কামনা করছি।
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">প্রধান শিক্ষক/মুহতামিম</div>
                <div class="signature-title">স্বাক্ষর ও সীল</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">অফিস কর্মকর্তা</div>
                <div class="signature-title">স্বাক্ষর</div>
            </div>
        </div>

        <div class="footer">
            এই ছাড়পত্র শুধুমাত্র অফিসিয়াল ব্যবহারের জন্য বৈধ। কোনো ধরনের পরিবর্তন করা হলে এটি বাতিল বলে গণ্য হবে।
        </div>
    </div>
</body>

</html>