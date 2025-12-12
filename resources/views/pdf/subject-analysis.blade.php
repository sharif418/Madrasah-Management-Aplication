<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>বিষয় বিশ্লেষণ</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 14pt;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 9pt;
            color: #666;
        }

        .report-title {
            text-align: center;
            margin: 10px 0;
            font-size: 12pt;
            font-weight: bold;
            background: #4a5568;
            color: white;
            padding: 8px;
            border-radius: 5px;
        }

        .overall-stats {
            display: flex;
            justify-content: space-around;
            margin: 15px 0;
            padding: 10px;
            background: #e8f4ff;
            border-radius: 8px;
        }

        .stat-box {
            text-align: center;
        }

        .stat-box .value {
            font-size: 16pt;
            font-weight: bold;
            color: #2c5282;
        }

        .stat-box .label {
            font-size: 8pt;
            color: #666;
        }

        .subject-section {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            page-break-inside: avoid;
        }

        .subject-header {
            font-size: 11pt;
            font-weight: bold;
            color: #2d5a27;
            border-bottom: 1px solid #2d5a27;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .stats-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .stat-card {
            flex: 1;
            padding: 8px;
            text-align: center;
            border-radius: 5px;
        }

        .stat-card.total {
            background: #e3f2fd;
            color: #1565c0;
        }

        .stat-card.passed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .stat-card.failed {
            background: #ffebee;
            color: #c62828;
        }

        .stat-card.absent {
            background: #f5f5f5;
            color: #757575;
        }

        .stat-card .num {
            font-size: 14pt;
            font-weight: bold;
        }

        .stat-card .lbl {
            font-size: 7pt;
        }

        .marks-stats {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }

        .marks-stat {
            flex: 1;
            padding: 5px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .marks-stat .val {
            font-size: 12pt;
            font-weight: bold;
        }

        .marks-stat.highest .val {
            color: #8e24aa;
        }

        .marks-stat.average .val {
            color: #f9a825;
        }

        .marks-stat.lowest .val {
            color: #e65100;
        }

        .pass-bar {
            height: 15px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .pass-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #4caf50, #81c784);
        }

        .grade-row {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }

        .grade-box {
            flex: 1;
            text-align: center;
            padding: 5px;
            border-radius: 3px;
            font-size: 8pt;
        }

        .grade-box.aplus {
            background: #a5d6a7;
        }

        .grade-box.a {
            background: #c5e1a5;
        }

        .grade-box.aminus {
            background: #dcedc8;
        }

        .grade-box.b {
            background: #bbdefb;
        }

        .grade-box.c {
            background: #fff9c4;
        }

        .grade-box.d {
            background: #ffccbc;
        }

        .grade-box.f {
            background: #ffcdd2;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8pt;
            color: #888;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $institute['name'] ?? 'প্রতিষ্ঠানের নাম' }}</h1>
        <p>{{ $institute['address'] ?? '' }}</p>
    </div>

    <div class="report-title">
        বিষয়ভিত্তিক পারফরম্যান্স বিশ্লেষণ<br>
        <small style="font-weight: normal;">{{ $analysisData['exam']->name ?? '' }} |
            {{ $analysisData['class']->name ?? '' }}</small>
    </div>

    <div class="overall-stats">
        <div class="stat-box">
            <div class="value">{{ $analysisData['overall']['total_subjects'] }}</div>
            <div class="label">মোট বিষয়</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ number_format($analysisData['overall']['average_pass_rate'], 1) }}%</div>
            <div class="label">গড় পাস হার</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ $analysisData['overall']['overall_average'] }}</div>
            <div class="label">গড় নম্বর</div>
        </div>
    </div>

    @foreach($analysisData['subjects'] as $subject)
        <div class="subject-section">
            <div class="subject-header">
                📚 {{ $subject['subject_name'] }} (পূর্ণ: {{ $subject['full_marks'] }}, পাস: {{ $subject['pass_marks'] }})
            </div>

            <div class="stats-row">
                <div class="stat-card total">
                    <div class="num">{{ $subject['total_students'] }}</div>
                    <div class="lbl">মোট</div>
                </div>
                <div class="stat-card passed">
                    <div class="num">{{ $subject['passed'] }}</div>
                    <div class="lbl">পাস</div>
                </div>
                <div class="stat-card failed">
                    <div class="num">{{ $subject['failed'] }}</div>
                    <div class="lbl">ফেল</div>
                </div>
                <div class="stat-card absent">
                    <div class="num">{{ $subject['absent'] }}</div>
                    <div class="lbl">অনু.</div>
                </div>
            </div>

            <div style="font-size: 8pt; margin-bottom: 3px;">পাস হার: <strong>{{ $subject['pass_percentage'] }}%</strong>
            </div>
            <div class="pass-bar">
                <div class="pass-bar-fill" style="width: {{ $subject['pass_percentage'] }}%;"></div>
            </div>

            <div class="marks-stats">
                <div class="marks-stat highest">
                    <div class="val">{{ $subject['highest'] }}</div>
                    <div style="font-size: 7pt;">সর্বোচ্চ</div>
                </div>
                <div class="marks-stat average">
                    <div class="val">{{ $subject['average'] }}</div>
                    <div style="font-size: 7pt;">গড়</div>
                </div>
                <div class="marks-stat lowest">
                    <div class="val">{{ $subject['lowest'] }}</div>
                    <div style="font-size: 7pt;">সর্বনিম্ন</div>
                </div>
            </div>

            <div style="font-size: 8pt; margin-top: 8px;">গ্রেড বিতরণ:</div>
            <div class="grade-row">
                <div class="grade-box aplus"><strong>{{ $subject['grade_distribution']['A+'] }}</strong><br>A+</div>
                <div class="grade-box a"><strong>{{ $subject['grade_distribution']['A'] }}</strong><br>A</div>
                <div class="grade-box aminus"><strong>{{ $subject['grade_distribution']['A-'] }}</strong><br>A-</div>
                <div class="grade-box b"><strong>{{ $subject['grade_distribution']['B'] }}</strong><br>B</div>
                <div class="grade-box c"><strong>{{ $subject['grade_distribution']['C'] }}</strong><br>C</div>
                <div class="grade-box d"><strong>{{ $subject['grade_distribution']['D'] }}</strong><br>D</div>
                <div class="grade-box f"><strong>{{ $subject['grade_distribution']['F'] }}</strong><br>F</div>
            </div>
        </div>
    @endforeach

    <div class="footer">
        তৈরির তারিখ: {{ $analysisData['generated_at']->format('d M Y, h:i A') }}
    </div>
</body>

</html>