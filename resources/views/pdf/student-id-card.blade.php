<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>ছাত্র আইডি কার্ড - {{ $student->admission_no }}</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Hind Siliguri', 'Noto Sans Bengali', sans-serif;
            font-size: 8pt;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #1f2937;
        }

        .card {
            width: 3.375in;
            height: 2.125in;
            padding: 8px;
            background: white;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .header {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            padding: 5px 0;
        }

        .header h1 {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header .subtitle {
            font-size: 6pt;
            opacity: 0.9;
        }

        .content {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .photo {
            width: 60px;
            height: 70px;
            border: 2px solid #059669;
            border-radius: 4px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info {
            flex: 1;
        }

        .info-row {
            display: flex;
            margin-bottom: 3px;
        }

        .label {
            font-size: 6pt;
            color: #6b7280;
            width: 45px;
        }

        .value {
            font-size: 7pt;
            font-weight: 600;
            color: #1f2937;
        }

        .name {
            font-size: 10pt;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }

        .footer {
            position: absolute;
            bottom: 5px;
            left: 8px;
            right: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 5pt;
            color: #9ca3af;
        }

        .id-badge {
            position: absolute;
            top: 45px;
            right: 8px;
            background: #059669;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            <h1>মাদরাসা ম্যানেজমেন্ট</h1>
            <div class="subtitle">ছাত্র পরিচয়পত্র</div>
        </div>

        <div class="id-badge">{{ $student->admission_no }}</div>

        <div class="content">
            <div class="photo">
                @if($student->photo)
                    <img src="{{ storage_path('app/public/' . $student->photo) }}" alt="Photo">
                @else
                    <div
                        style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af; font-size: 20pt;">
                        👤</div>
                @endif
            </div>
            <div class="info">
                <div class="name">{{ $student->name }}</div>
                <div class="info-row">
                    <span class="label">শ্রেণি:</span>
                    <span class="value">{{ $student->class?->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">শাখা:</span>
                    <span class="value">{{ $student->section?->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">রোল:</span>
                    <span class="value">{{ $student->roll_no ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">পিতা:</span>
                    <span class="value">{{ $student->father_name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">মোবাইল:</span>
                    <span class="value">{{ $student->father_phone ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <span>শিক্ষাবর্ষ: {{ $student->academicYear?->name ?? date('Y') }}</span>
            <span>রক্তের গ্রুপ: {{ $student->blood_group ?? '-' }}</span>
        </div>
    </div>
</body>

</html>