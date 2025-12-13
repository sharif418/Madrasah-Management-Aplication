<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>কর্মচারী আইডি কার্ড</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kalpurush', 'SolaimanLipi', sans-serif;
            background: #f5f5f5;
        }

        .card-container {
            width: 85.6mm;
            height: 53.98mm;
            margin: 10mm auto;
            background: linear-gradient(135deg, #1a5f7a 0%, #2c3e50 100%);
            border-radius: 8px;
            padding: 4mm;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card-header {
            text-align: center;
            margin-bottom: 3mm;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 2mm;
        }

        .institution-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .card-type {
            font-size: 7pt;
            background: #e74c3c;
            padding: 1mm 3mm;
            border-radius: 2mm;
            display: inline-block;
        }

        .card-body {
            display: flex;
            gap: 3mm;
        }

        .photo-section {
            width: 22mm;
        }

        .photo {
            width: 22mm;
            height: 26mm;
            background: #fff;
            border-radius: 3px;
            overflow: hidden;
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-section {
            flex: 1;
        }

        .info-row {
            margin-bottom: 1.5mm;
            font-size: 7pt;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 6pt;
        }

        .info-value {
            font-weight: bold;
            font-size: 8pt;
        }

        .employee-id {
            background: rgba(255, 255, 255, 0.2);
            padding: 1mm 2mm;
            border-radius: 2mm;
            font-size: 9pt;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 2mm;
        }

        .card-footer {
            position: absolute;
            bottom: 2mm;
            left: 4mm;
            right: 4mm;
            text-align: center;
            font-size: 5pt;
            color: rgba(255, 255, 255, 0.6);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 1mm;
        }

        .watermark {
            position: absolute;
            right: -10mm;
            bottom: -10mm;
            font-size: 50pt;
            color: rgba(255, 255, 255, 0.05);
            font-weight: bold;
            transform: rotate(-30deg);
        }

        /* Back side */
        .card-back {
            width: 85.6mm;
            height: 53.98mm;
            margin: 10mm auto;
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-radius: 8px;
            padding: 4mm;
            color: #2c3e50;
            position: relative;
        }

        .back-title {
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 1mm;
        }

        .terms {
            font-size: 6pt;
            line-height: 1.6;
        }

        .terms li {
            margin-bottom: 1mm;
        }

        .signature-area {
            position: absolute;
            bottom: 4mm;
            right: 4mm;
            text-align: center;
        }

        .signature-line {
            width: 25mm;
            border-top: 1px solid #2c3e50;
            margin-bottom: 1mm;
        }

        .signature-label {
            font-size: 6pt;
        }

        .validity {
            position: absolute;
            bottom: 4mm;
            left: 4mm;
            font-size: 6pt;
        }

        .validity-value {
            font-weight: bold;
            color: #e74c3c;
        }
    </style>
</head>

<body>
    <!-- Front Side -->
    <div class="card-container">
        <div class="watermark">STAFF</div>

        <div class="card-header">
            <div class="institution-name">{{ institution_name() }}</div>
            <span class="card-type">কর্মচারী পরিচয়পত্র</span>
        </div>

        <div class="card-body">
            <div class="photo-section">
                <div class="photo">
                    @if($staff->getFirstMediaUrl('photo'))
                        <img src="{{ $staff->getFirstMediaUrl('photo') }}" alt="Photo">
                    @else
                        <img src="{{ asset('images/default-staff.png') }}" alt="Photo">
                    @endif
                </div>
            </div>

            <div class="info-section">
                <div class="employee-id">{{ $staff->employee_id }}</div>

                <div class="info-row">
                    <div class="info-label">নাম</div>
                    <div class="info-value">{{ $staff->name }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">পদবী</div>
                    <div class="info-value">{{ $staff->designation?->title ?? '-' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">মোবাইল</div>
                    <div class="info-value">{{ $staff->phone }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">রক্তের গ্রুপ</div>
                    <div class="info-value">{{ $staff->blood_group ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            {{ institution_address() }} | {{ institution_phone() }}
        </div>
    </div>

    <!-- Back Side -->
    <div class="card-back">
        <div class="back-title">শর্তাবলী ও নির্দেশনা</div>

        <ul class="terms">
            <li>এই কার্ড শুধুমাত্র উল্লেখিত ব্যক্তির জন্য প্রযোজ্য।</li>
            <li>কার্ড হারিয়ে গেলে কর্তৃপক্ষকে অবিলম্বে জানাতে হবে।</li>
            <li>কার্ড অন্য কাউকে দেওয়া বা ধার দেওয়া নিষেধ।</li>
            <li>প্রতিষ্ঠানে প্রবেশের সময় এই কার্ড প্রদর্শন করা বাধ্যতামূলক।</li>
            <li>মেয়াদ শেষ হলে কার্ড নবায়ন করতে হবে।</li>
        </ul>

        <div class="validity">
            <div>মেয়াদ:</div>
            <div class="validity-value">{{ now()->addYear()->format('d/m/Y') }}</div>
        </div>

        <div class="signature-area">
            <div class="signature-line"></div>
            <div class="signature-label">অধ্যক্ষ/প্রিন্সিপাল</div>
        </div>
    </div>
</body>

</html>