<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>আসন বিন্যাস</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }

        .room-page {
            page-break-after: always;
            padding: 10px;
        }

        .room-page:last-child {
            page-break-after: auto;
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

        .exam-title {
            text-align: center;
            margin: 10px 0;
            font-size: 12pt;
            font-weight: bold;
            padding: 6px;
            background: #f0f0f0;
            border-radius: 5px;
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
            padding: 8px;
            background: #2d5a27;
            color: white;
            border-radius: 5px;
        }

        .room-header h2 {
            font-size: 14pt;
        }

        .room-header .stats {
            font-size: 10pt;
        }

        .seat-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 3px;
        }

        .seat-grid td {
            padding: 5px;
            text-align: center;
            vertical-align: top;
        }

        .row-number {
            width: 25px;
            font-weight: bold;
            color: #666;
            font-size: 10pt;
        }

        .seat-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            min-width: 100px;
            background: #fafafa;
        }

        .seat-box .class-roll {
            font-size: 7pt;
            color: #888;
            margin-bottom: 2px;
        }

        .seat-box .name {
            font-size: 9pt;
            font-weight: bold;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
        }

        .seat-box .seat-num {
            font-size: 7pt;
            color: #aaa;
            margin-top: 2px;
        }

        .seat-empty {
            border: 1px dashed #ccc;
            border-radius: 4px;
            padding: 5px;
            min-width: 100px;
            background: #fff;
            color: #ccc;
        }

        .summary-box {
            margin-top: 10px;
            padding: 8px;
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .value {
            font-size: 14pt;
            font-weight: bold;
            color: #2d5a27;
        }

        .summary-item .label {
            font-size: 8pt;
            color: #666;
        }

        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            text-align: center;
            font-size: 8pt;
            color: #888;
        }

        .blackboard {
            background: linear-gradient(180deg, #2a4d29, #1a3a19);
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    @foreach($seatPlanData['rooms'] as $roomIndex => $room)
        <div class="room-page">
            {{-- Header --}}
            <div class="header">
                <h1>{{ $institute['name'] ?? 'প্রতিষ্ঠানের নাম' }}</h1>
                <p>{{ $institute['address'] ?? '' }}</p>
            </div>

            {{-- Exam Title --}}
            <div class="exam-title">
                {{ $seatPlanData['exam']->name ?? 'পরীক্ষা' }} - আসন বিন্যাস
                <br>
                <span style="font-size: 10pt; font-weight: normal;">
                    শ্রেণি: {{ implode(', ', $seatPlanData['classes']) }}
                </span>
            </div>

            {{-- Room Header --}}
            <div class="room-header">
                <h2>🚪 রুম: {{ $room['room_name'] }}</h2>
                <div class="stats">
                    ছাত্র সংখ্যা: {{ $room['total_students'] }} জন
                </div>
            </div>

            {{-- Blackboard --}}
            <div class="blackboard">
                ব্ল্যাকবোর্ড / শিক্ষক টেবিল
            </div>

            {{-- Seat Grid --}}
            <table class="seat-grid">
                <tbody>
                    @foreach($room['rows'] as $rowIndex => $row)
                        <tr>
                            <td class="row-number">{{ $rowIndex + 1 }}</td>
                            @foreach($row as $seat)
                                <td>
                                    <div class="seat-box">
                                        <div class="class-roll">{{ $seat['class'] }} | রোল {{ $seat['roll'] ?? '-' }}</div>
                                        <div class="name" title="{{ $seat['name'] }}">{{ $seat['name'] }}</div>
                                        <div class="seat-num">আসন: {{ $seat['seat'] }}</div>
                                    </div>
                                </td>
                            @endforeach
                            @for($i = count($row); $i < $seatPlanData['students_per_row']; $i++)
                                <td>
                                    <div class="seat-empty">-</div>
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Footer --}}
            <div class="footer">
                রুম {{ $roomIndex + 1 }} / {{ count($seatPlanData['rooms']) }} |
                তৈরির তারিখ: {{ $seatPlanData['generated_at']->format('d M Y, h:i A') }}
            </div>
        </div>
    @endforeach

    {{-- Summary Page --}}
    <div class="room-page">
        <div class="header">
            <h1>{{ $institute['name'] ?? 'প্রতিষ্ঠানের নাম' }}</h1>
            <p>আসন বিন্যাস সামারি</p>
        </div>

        <div class="exam-title">
            {{ $seatPlanData['exam']->name ?? 'পরীক্ষা' }}
        </div>

        <div class="summary-box" style="margin-top: 30px;">
            <div class="summary-item">
                <div class="value">{{ $seatPlanData['total_students'] }}</div>
                <div class="label">মোট ছাত্র</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ $seatPlanData['allocated'] }}</div>
                <div class="label">বসানো হয়েছে</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ count($seatPlanData['rooms']) }}</div>
                <div class="label">মোট রুম</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ $seatPlanData['students_per_row'] }}</div>
                <div class="label">প্রতি সারিতে</div>
            </div>
        </div>

        <h3 style="margin-top: 30px; font-size: 12pt;">রুম অনুযায়ী ছাত্র বিভাজন:</h3>
        <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">রুম</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">ছাত্র সংখ্যা</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">সারি সংখ্যা</th>
                </tr>
            </thead>
            <tbody>
                @foreach($seatPlanData['rooms'] as $room)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $room['room_name'] }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $room['total_students'] }}
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ count($room['rows']) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer" style="margin-top: 50px;">
            তৈরির তারিখ: {{ $seatPlanData['generated_at']->format('d M Y, h:i A') }}
        </div>
    </div>
</body>

</html>