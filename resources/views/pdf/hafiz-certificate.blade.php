<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>হাফেজ সার্টিফিকেট</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
        }

        .certificate {
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            padding: 40px;
            position: relative;
        }

        .border-frame {
            border: 3px solid #047857;
            padding: 30px;
            min-height: calc(100vh - 80px);
            position: relative;
        }

        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: #047857;
        }

        .tl {
            top: 10px;
            left: 10px;
            border-top: 4px solid;
            border-left: 4px solid;
        }

        .tr {
            top: 10px;
            right: 10px;
            border-top: 4px solid;
            border-right: 4px solid;
        }

        .bl {
            bottom: 10px;
            left: 10px;
            border-bottom: 4px solid;
            border-left: 4px solid;
        }

        .br {
            bottom: 10px;
            right: 10px;
            border-bottom: 4px solid;
            border-right: 4px solid;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .bismillah {
            font-size: 28px;
            color: #047857;
            margin-bottom: 15px;
        }

        .institution-name {
            font-size: 28px;
            font-weight: bold;
            color: #065f46;
            margin-bottom: 5px;
        }

        .institution-address {
            font-size: 12px;
            color: #6b7280;
        }

        .title-section {
            text-align: center;
            margin: 30px 0;
        }

        .title {
            font-size: 36px;
            font-weight: bold;
            color: #047857;
            letter-spacing: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-top: 10px;
        }

        .content {
            text-align: center;
            margin: 40px 0;
        }

        .content p {
            font-size: 16px;
            line-height: 2.2;
            margin: 10px 0;
        }

        .student-name {
            font-size: 28px;
            font-weight: bold;
            color: #065f46;
            text-decoration: underline;
        }

        .father-name {
            font-size: 18px;
            color: #047857;
        }

        .achievement-box {
            background: #ecfdf5;
            border: 2px solid #a7f3d0;
            border-radius: 10px;
            padding: 20px;
            margin: 30px auto;
            max-width: 500px;
            text-align: center;
        }

        .achievement-title {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
            margin-bottom: 10px;
        }

        .achievement-detail {
            font-size: 14px;
            color: #065f46;
        }

        .footer {
            display: table;
            width: 100%;
            margin-top: 60px;
        }

        .footer-item {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 1px solid #047857;
            width: 150px;
            margin: 0 auto;
            padding-top: 8px;
        }

        .signature-title {
            font-size: 11px;
            color: #6b7280;
        }

        .certificate-no {
            position: absolute;
            bottom: 15px;
            left: 30px;
            font-size: 10px;
            color: #9ca3af;
        }

        .issue-date {
            position: absolute;
            bottom: 15px;
            right: 30px;
            font-size: 10px;
            color: #9ca3af;
        }

        .quran-verse {
            text-align: center;
            font-size: 16px;
            color: #047857;
            font-style: italic;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="certificate">
        <div class="border-frame">
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>

            <div class="header">
                <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>
                <div class="institution-name">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
                <div class="institution-address">{{ institution_address() ?? 'ঠিকানা' }}</div>
            </div>

            <div class="title-section">
                <div class="title">হাফেজ সার্টিফিকেট</div>
                <div class="subtitle">Certificate of Hifz-ul-Quran Completion</div>
            </div>

            <div class="content">
                <p>এই মর্মে প্রত্যয়ন করা যাচ্ছে যে,</p>
                <p class="student-name">{{ $student->name ?? 'ছাত্রের নাম' }}</p>
                <p class="father-name">পিতা: {{ $student->father_name ?? '-' }}</p>
                <p>
                    সফলতার সাথে পবিত্র কুরআন মাজীদ সম্পূর্ণ ৩০ পারা<br>
                    মুখস্থ করে হাফেজে কুরআন উপাধি অর্জন করেছেন।
                </p>
            </div>

            <div class="achievement-box">
                <div class="achievement-title">🕌 সম্পন্ন: ৩০/৩০ পারা</div>
                <div class="achievement-detail">
                    শুরু: {{ $summary['start_date'] ?? '-' }} | সমাপ্তি: {{ $summary['completion_date'] ?? '-' }}
                </div>
            </div>

            <div class="quran-verse">
                "خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ"<br>
                <small>তোমাদের মধ্যে সর্বোত্তম সে, যে কুরআন শিক্ষা করে এবং শিক্ষা দেয়</small>
            </div>

            <div class="footer">
                <div class="footer-item">
                    <div class="signature-line">
                        হিফজ বিভাগ প্রধান
                    </div>
                </div>
                <div class="footer-item">
                    <div style="font-size: 40px; color: #047857;">🏆</div>
                </div>
                <div class="footer-item">
                    <div class="signature-line">
                        অধ্যক্ষ / পরিচালক
                    </div>
                </div>
            </div>

            <div class="certificate-no">সার্টিফিকেট নং: {{ $certificateNo }}</div>
            <div class="issue-date">ইস্যু তারিখ: {{ $issueDate }}</div>
        </div>
    </div>
</body>

</html>