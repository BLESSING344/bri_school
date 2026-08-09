<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #198754;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .school-name {
            color: #198754;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 14px;
            color: #555;
            margin: 0;
        }
        .student-info {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 6px 4px;
            font-size: 14px;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .marks-table th, .marks-table td {
            border: 1px solid #b2b2b2;
            padding: 8px 10px;
            text-align: left;
            font-size: 13px;
        }
        .marks-table th {
            background-color: #198754;
            color: #ffffff;
            font-weight: bold;
        }
        .marks-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 45%;
            float: right;
            text-align: center;
            border-top: 1px dashed #555;
            padding-top: 5px;
            font-size: 13px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="school-name">BRI INTERNATIONAL SCHOOL</h1>
        <p class="subtitle">Official Student Academic Progress Report</p>
    </div>

    <table class="student-info">
        <tr>
            <td width="60%"><strong>Student Name:</strong> {{ $student['name'] }}</td>
            <td width="40%"><strong>Class / Grade:</strong> {{ $student['class'] }}</td>
        </tr>
        <tr>
            <td><strong>Gender:</strong> {{ $student['gender'] }}</td>
            <td><strong>Parent Contact:</strong> {{ $student['parent_contact'] }}</td>
        </tr>
    </table>

    <h3>Academic Performance</h3>
    <table class="marks-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Mark (%)</th>
                <th>Grade</th>
                <th>Teacher Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $item)
            <tr>
                <td>{{ $item->subject_name ?? $item->subject }}</td>
                <td>{{ $item->mark ?? $item->score }}%</td>
                <td>{{ $item->grade ?? 'D1' }}</td>
                <td>{{ $item->teacher ?? 'Very Good' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-section">
        <div class="signature-box">
            <p><strong>Head Teacher / Principal</strong></p>
            <p style="font-style: italic; color: #777;">Authorized Stamp & Signature</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>