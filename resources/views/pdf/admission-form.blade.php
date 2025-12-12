<!DOCTYPE html>
<html lang="bn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ভর্তি ফর্ম - {{ $application->application_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Bengali', 'Kalpurush', sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        .container {
            padding: 30px;
            max-width: 700px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            color: #1a5276;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #7f8c8d;
        }

        .header .form-title {
            margin-top: 15px;
            font-size: 16px;
            padding: 8px 25px;
            background: #27ae60;
            color: #fff;
            display: inline-block;
            border-radius: 3px;
        }

        .application-no {
            text-align: right;
            margin-bottom: 15px;
        }

        .application-no span {
            background: #3498db;
            color: #fff;
            padding: 5px 15px;
            font-weight: bold;
            font-size: 12px;
        }

        .photo-section {
            float: right;
            width: 100px;
            height: 120px;
            border: 2px solid #2c3e50;
            text-align: center;
            line-height: 120px;
            margin-left: 20px;
            margin-bottom: 10px;
            background: #ecf0f1;
        }

        .section {
            margin-bottom: 20px;
            clear: both;
        }

        .section-title {
            background: #1a5276;
            color: #fff;
            padding: 8px 15px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px 10px;
            border: 1px solid #bdc3c7;
            vertical-align: top;
        }

        .info-table .label {
            background: #f8f9fa;
            font-weight: bold;
            width: 150px;
            color: #2c3e50;
        }

        .info-table .value {
            min-height: 20px;
        }

        .declaration {
            margin-top: 25px;
            padding: 15px;
            background: #fef9e7;
            border-left: 4px solid #f1c40f;
        }

        .declaration h4 {
            color: #d68910;
            margin-bottom: 10px;
        }

        .declaration p {
            font-size: 10px;
            color: #7b7d7d;
            text-align: justify;
        }

        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding-top: 40px;
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

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }

        .status-pending {
            background: #fef9e7;
            color: #9a7d0a;
        }

        .status-approved {
            background: #d5f5e3;
            color: #1e8449;
        }

        .status-rejected {
            background: #fadbd8;
            color: #922b21;
        }

        .status-admitted {
            background: #d4e6f1;
            color: #1a5276;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #95a5a6;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }

        .office-use {
            margin-top: 30px;
            border: 2px solid #e74c3c;
            padding: 15px;
        }

        .office-use h4 {
            color: #e74c3c;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $institute['name'] }}</h1>
            <p>{{ $institute['address'] }}</p>
            @if($institute['phone'])
                <p>ফোন: {{ $institute['phone'] }} | ইমেইল: {{ $institute['email'] }}</p>
            @endif
            <div class="form-title">ভর্তি আবেদন ফর্ম</div>
        </div>

        <!-- Application No -->
        <div class="application-no">
            <span>আবেদন নং: {{ $application->application_no }}</span>
        </div>

        <!-- Photo -->
        <div class="photo-section">
            ছবি<br>(পাসপোর্ট সাইজ)
        </div>

        <!-- Student Info -->
        <div class="section">
            <div class="section-title">ছাত্র/ছাত্রী তথ্য</div>
            <table class="info-table">
                <tr>
                    <td class="label">নাম (বাংলা):</td>
                    <td class="value">{{ $application->student_name }}</td>
                    <td class="label">নাম (ইংরেজি):</td>
                    <td class="value">{{ $application->student_name_en ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">জন্ম তারিখ:</td>
                    <td class="value">{{ $application->date_of_birth?->format('d/m/Y') ?? '' }}</td>
                    <td class="label">লিঙ্গ:</td>
                    <td class="value">
                        {{ \App\Models\AdmissionApplication::genderOptions()[$application->gender] ?? $application->gender }}
                    </td>
                </tr>
                <tr>
                    <td class="label">রক্তের গ্রুপ:</td>
                    <td class="value">{{ $application->blood_group ?? '' }}</td>
                    <td class="label">জন্মনিবন্ধন নং:</td>
                    <td class="value">{{ $application->birth_certificate_no ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">শ্রেণি (আবেদিত):</td>
                    <td class="value" colspan="3"><strong>{{ $application->class?->name ?? 'N/A' }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Guardian Info -->
        <div class="section">
            <div class="section-title">অভিভাবক তথ্য</div>
            <table class="info-table">
                <tr>
                    <td class="label">পিতার নাম:</td>
                    <td class="value">{{ $application->father_name }}</td>
                    <td class="label">পিতার মোবাইল:</td>
                    <td class="value">{{ $application->father_phone }}</td>
                </tr>
                <tr>
                    <td class="label">পিতার পেশা:</td>
                    <td class="value" colspan="3">{{ $application->father_occupation ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">মাতার নাম:</td>
                    <td class="value">{{ $application->mother_name }}</td>
                    <td class="label">মাতার মোবাইল:</td>
                    <td class="value">{{ $application->mother_phone ?? '' }}</td>
                </tr>
            </table>
        </div>

        <!-- Address -->
        <div class="section">
            <div class="section-title">ঠিকানা</div>
            <table class="info-table">
                <tr>
                    <td class="label">বর্তমান ঠিকানা:</td>
                    <td class="value" colspan="3">{{ $application->present_address }}</td>
                </tr>
                <tr>
                    <td class="label">স্থায়ী ঠিকানা:</td>
                    <td class="value" colspan="3">{{ $application->permanent_address ?? $application->present_address }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Declaration -->
        <div class="declaration">
            <h4>অঙ্গীকারনামা:</h4>
            <p>
                আমি অঙ্গীকার করছি যে, উপরোক্ত তথ্যাদি সম্পূর্ণ সত্য ও সঠিক। কোন তথ্য মিথ্যা প্রমাণিত হলে
                আমার সন্তানের ভর্তি বাতিল হতে পারে এবং এজন্য আমি কোন প্রকার আপত্তি করব না।
                আমি প্রতিষ্ঠানের সকল নিয়ম-কানুন মেনে চলতে এবং সন্তানকে মেনে চলতে বাধ্য করব।
            </p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="line"></div>
                <span>আবেদনকারীর স্বাক্ষর</span>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <span>তারিখ</span>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <span>অভিভাবকের স্বাক্ষর</span>
            </div>
        </div>

        <!-- Office Use Only -->
        <div class="office-use">
            <h4>অফিস ব্যবহারের জন্য</h4>
            <table class="info-table">
                <tr>
                    <td class="label">স্ট্যাটাস:</td>
                    <td class="value">
                        <span class="status-badge status-{{ $application->status }}">
                            {{ \App\Models\AdmissionApplication::statusOptions()[$application->status] ?? $application->status }}
                        </span>
                    </td>
                    <td class="label">আবেদনের তারিখ:</td>
                    <td class="value">{{ $application->created_at?->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">নিবন্ধন ফি:</td>
                    <td class="value"></td>
                    <td class="label">রসিদ নং:</td>
                    <td class="value"></td>
                </tr>
                @if($application->remarks)
                    <tr>
                        <td class="label">মন্তব্য:</td>
                        <td class="value" colspan="3">{{ $application->remarks }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            Generated on: {{ $generated_at }} | {{ $institute['name'] }}
        </div>
    </div>
</body>

</html>