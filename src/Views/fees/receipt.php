<?php
$receiptNo = 'BRI-' . str_pad((string) $payment['id'], 6, '0', STR_PAD_LEFT);
$balance = (float) $payment['balance'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt <?php echo htmlspecialchars($receiptNo); ?> - BRI International School</title>
    <link rel="icon" href="/images/fevicon.png" type="image/png" />
    <style>
        :root {
            --brand: #2f8a5b;
            --ink: #2b2b2b;
            --muted: #6b7280;
            --line: #e3e6ea;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef1f4;
            color: var(--ink);
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
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            text-decoration: none;
            cursor: pointer;
        }
        .toolbar .primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }
        .receipt {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .receipt-head {
            background: var(--brand);
            color: #fff;
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .receipt-head h1 {
            margin: 0 0 4px;
            font-size: 20px;
        }
        .receipt-head p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .receipt-head .badge-paid {
            text-align: right;
        }
        .receipt-head .badge-paid .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: 0.85;
        }
        .receipt-head .badge-paid .no {
            font-size: 18px;
            font-weight: 600;
        }
        .receipt-body {
            padding: 32px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 1px dashed var(--line);
        }
        .meta-grid .field .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
            margin-bottom: 3px;
        }
        .meta-grid .field .value {
            font-size: 15px;
            font-weight: 600;
        }
        table.amount-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.amount-table th, table.amount-table td {
            text-align: left;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
            font-size: 14px;
        }
        table.amount-table th {
            color: var(--muted);
            font-weight: 500;
        }
        table.amount-table td.amount, table.amount-table th.amount {
            text-align: right;
        }
        table.amount-table tr.total td {
            font-size: 17px;
            font-weight: 700;
            border-bottom: none;
            padding-top: 16px;
        }
        .status-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .status-line.paid { background: #eaf7ef; color: #1e7a45; }
        .status-line.due { background: #fdeeee; color: #b13a3a; }
        .status-line .amt { font-weight: 700; }
        .footer-note {
            margin-top: 24px;
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature div {
            width: 45%;
            border-top: 1px solid var(--line);
            padding-top: 6px;
            font-size: 12px;
            color: var(--muted);
            text-align: center;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .receipt { box-shadow: none; border-radius: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="/dashboard/fees">&larr; Back to Fees</a>
        <button type="button" class="primary" onclick="window.print()">Print Receipt</button>
    </div>

    <div class="receipt">
        <div class="receipt-head">
            <div>
                <h1>BRI International School</h1>
                <p>Official Payment Receipt</p>
            </div>
            <div class="badge-paid">
                <div class="label">Receipt No.</div>
                <div class="no"><?php echo htmlspecialchars($receiptNo); ?></div>
            </div>
        </div>

        <div class="receipt-body">
            <div class="meta-grid">
                <div class="field">
                    <div class="label">Student</div>
                    <div class="value"><?php echo htmlspecialchars($payment['student_name']); ?></div>
                </div>
                <div class="field">
                    <div class="label">Class</div>
                    <div class="value"><?php echo htmlspecialchars($payment['class']); ?></div>
                </div>
                <div class="field">
                    <div class="label">Term</div>
                    <div class="value"><?php echo htmlspecialchars($payment['term'] ?? 'N/A'); ?></div>
                </div>
                <div class="field">
                    <div class="label">Payment Date</div>
                    <div class="value"><?php echo date('F j, Y', strtotime($payment['payment_date'])); ?></div>
                </div>
                <?php if (!empty($payment['parent_name'])): ?>
                <div class="field">
                    <div class="label">Parent / Guardian</div>
                    <div class="value"><?php echo htmlspecialchars($payment['parent_name']); ?></div>
                </div>
                <?php endif; ?>
                <div class="field">
                    <div class="label">Recorded By</div>
                    <div class="value"><?php echo htmlspecialchars($payment['recorded_by'] ?? 'N/A'); ?></div>
                </div>
            </div>

            <table class="amount-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['term'] ?? 'School Fees'); ?> payment</td>
                        <td class="amount"><?php echo number_format((float) $payment['amount_paid'], 2); ?></td>
                    </tr>
                    <tr class="total">
                        <td>Amount Paid</td>
                        <td class="amount"><?php echo number_format((float) $payment['amount_paid'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <?php if ($balance > 0): ?>
                <div class="status-line due">
                    <span>Outstanding Balance</span>
                    <span class="amt"><?php echo number_format($balance, 2); ?></span>
                </div>
            <?php else: ?>
                <div class="status-line paid">
                    <span>Payment Status</span>
                    <span class="amt">Paid in Full</span>
                </div>
            <?php endif; ?>

            <div class="signature">
                <div>Bursar's Signature</div>
                <div>Official Stamp</div>
            </div>

            <p class="footer-note">
                This receipt was generated electronically and is valid without a physical signature.<br>
                Thank you for your payment.
            </p>
        </div>
    </div>
</body>
</html>
