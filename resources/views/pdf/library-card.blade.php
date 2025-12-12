<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>লাইব্রেরি কার্ড</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nikosh', 'SolaimanLipi', Arial, sans-serif;
            font-size: 10px;
        }

        .page {
            padding: 10mm;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8mm;
        }

        .card {
            width: 85mm;
            height: 55mm;
            border: 2px solid #1e40af;
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
            position: relative;
        }

        .card-header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 4mm;
            text-align: center;
        }

        .card-header .institution {
            font-size: 11px;
            font-weight: bold;
        }

        .card-header .title {
            font-size: 9px;
            margin-top: 2px;
        }

        .card-body {
            padding: 4mm;
            display: flex;
            gap: 3mm;
        }

        .photo-box {
            width: 20mm;
            height: 25mm;
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #6b7280;
            border-radius: 4px;
        }

        .details {
            flex: 1;
        }

        .details table {
            width: 100%;
            font-size: 9px;
        }

        .details td {
            padding: 1mm 0;
        }

        .details .label {
            color: #6b7280;
            width: 40%;
        }

        .details .value {
            font-weight: bold;
        }

        .member-id {
            font-size: 12px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 2mm;
        }

        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f3f4f6;
            padding: 2mm 4mm;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #6b7280;
        }

        .badge {
            display: inline-block;
            padding: 1mm 3mm;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-student {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-teacher {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-staff {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-external {
            background: #e5e7eb;
            color: #374151;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="cards-container">
            @foreach($members as $member)
                <div class="card">
                    <div class="card-header">
                        <div class="institution">{{ institution_name() ?? 'মাদরাসা নাম' }}</div>
                        <div class="title">লাইব্রেরি সদস্য কার্ড</div>
                    </div>
                    <div class="card-body">
                        <div class="photo-box">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div class="details">
                            <div class="member-id">{{ $member->member_id }}</div>
                            <table>
                                <tr>
                                    <td class="label">নাম:</td>
                                    <td class="value">{{ $member->name }}</td>
                                </tr>
                                <tr>
                                    <td class="label">ধরণ:</td>
                                    <td class="value">
                                        @php
                                            $typeClass = match ($member->member_type) {
                                                'student' => 'badge-student',
                                                'teacher' => 'badge-teacher',
                                                'staff' => 'badge-staff',
                                                default => 'badge-external',
                                            };
                                            $typeLabel = match ($member->member_type) {
                                                'student' => 'ছাত্র',
                                                'teacher' => 'শিক্ষক',
                                                'staff' => 'কর্মচারী',
                                                default => 'বহিরাগত',
                                            };
                                        @endphp
                                        <span class="badge {{ $typeClass }}">{{ $typeLabel }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">সর্বোচ্চ বই:</td>
                                    <td class="value">{{ $member->max_books }} টি</td>
                                </tr>
                                <tr>
                                    <td class="label">মোবাইল:</td>
                                    <td class="value">{{ $member->phone ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <span>সদস্য: {{ $member->membership_date?->format('d/m/Y') }}</span>
                        <span>মেয়াদ: {{ $member->expiry_date?->format('d/m/Y') ?? 'সীমাহীন' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>

</html>