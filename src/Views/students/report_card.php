<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Card - <?php echo htmlspecialchars($student['full_name']); ?></title>
    <link rel="icon" href="/images/fevicon.png" type="image/png" />
    <style>
        :root { --brand: #198754; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef1f4;
            color: #333;
            margin: 0;
            padding: 32px 16px;
        }
        .toolbar {
            max-width: 720px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .toolbar a, .toolbar button {
            font-family: inherit;
            font-size: 14px;
            padding: 9px 18px;
            border-radius: 6px;
            border: 1px solid #e3e6ea;
            background: #fff;
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }
        .toolbar .primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }
        .card {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            padding: 30px 40px 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double var(--brand);
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .school-name {
            color: var(--brand);
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
            background-color: var(--brand);
            color: #ffffff;
            font-weight: bold;
        }
        .marks-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .marks-table td.score { text-align: right; }
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
        .clear { clear: both; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .card { box-shadow: none; border-radius: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="/dashboard/students">&larr; Back to Students</a>
        <button type="button" class="primary" onclick="window.print()">Print Report Card</button>
    </div>

    <div class="card">
        <div class="header">
            <h1 class="school-name">BRI International School</h1>
            <p class="subtitle">Official Student Academic Progress Report</p>
        </div>

        <table class="student-info">
            <tr>
                <td width="60%"><strong>Student Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></td>
                <td width="40%"><strong>Class / Grade:</strong> <?php echo htmlspecialchars($student['class']); ?></td>
            </tr>
            <tr>
                <td><strong>Gender:</strong> <?php echo htmlspecialchars($student['gender']); ?></td>
                <td><strong>Parent Contact:</strong> <?php echo htmlspecialchars($student['parent_contact'] ?? 'N/A'); ?></td>
            </tr>
        </table>

        <h3>Academic Performance</h3>
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Exam</th>
                    <th>Subject</th>
                    <th>Mark (%)</th>
                    <th>Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($marks)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No marks recorded yet</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($marks as $mark): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($mark['exam_name']); ?> <?php echo htmlspecialchars($mark['term'] ? "({$mark['term']})" : ''); ?></td>
                        <td><?php echo htmlspecialchars($mark['subject']); ?></td>
                        <td class="score"><?php echo number_format((float) $mark['score'], 1); ?>%</td>
                        <td><?php echo htmlspecialchars($mark['grade']); ?></td>
                        <td><?php echo htmlspecialchars($mark['remark']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-section">
            <div class="signature-box">
                <p><strong>Head Teacher / Principal</strong></p>
                <p style="font-style: italic; color: #777;">Authorized Stamp &amp; Signature</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>
