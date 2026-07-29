<?php
require_once __DIR__ . '/includes/auth.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Payment ID.");
}

$id = $_GET['id'];

// Get payment and student details
$stmt = $pdo->prepare("SELECT f.*, s.full_name AS student_name, s.class 
                       FROM fees f 
                       JOIN students s ON f.student_id = s.id 
                       WHERE f.id = ?");
$stmt->execute([$id]);
$receipt = $stmt->fetch();

if (!$receipt) {
    die("Receipt not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $receipt['id']; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #eef2f5; margin: 0; padding: 40px 10px; }
        .receipt-box { max-width: 550px; background: #fff; margin: auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #e1e1e1; }
        .receipt-title { text-align: center; border-bottom: 2px solid #28a745; padding-bottom: 15px; margin-bottom: 25px; }
        .receipt-title h2 { margin: 0; color: #333; font-size: 24px; text-transform: uppercase; }
        .receipt-title p { margin: 5px 0 0; color: #777; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        td.label { font-weight: 600; color: #555; width: 40%; }
        td.value { color: #222; }
        .amount-highlight { font-size: 18px; color: #28a745; font-weight: bold; }
        .balance-highlight { font-size: 16px; color: #dc3545; font-weight: bold; }
        .btn-container { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #007bff; color: white; border: none; padding: 10px 25px; font-size: 15px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-print:hover { background: #0056b3; }
        .footer-note { text-align: center; font-size: 12px; color: #888; margin-top: 20px; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .receipt-box { box-shadow: none; border: none; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="btn-container no-print">
    <button onclick="window.print()" class="btn-print">🖨️ Print Receipt</button>
</div>

<div class="receipt-box">
    <div class="receipt-title">
        <h2>OFFICIAL PAYMENT RECEIPT</h2>
        <p>Receipt No: #REC-<?php echo str_pad($receipt['id'], 6, '0', STR_PAD_LEFT); ?></p>
    </div>

    <table>
        <tr>
            <td class="label">Student Name:</td>
            <td class="value"><strong><?php echo htmlspecialchars($receipt['student_name']); ?></strong></td>
        </tr>
        <tr>
            <td class="label">Class:</td>
            <td class="value"><?php echo htmlspecialchars($receipt['class']); ?></td>
        </tr>
        <tr>
            <td class="label">Term:</td>
            <td class="value"><?php echo htmlspecialchars($receipt['term'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td class="label">Amount Paid:</td>
            <td class="value amount-highlight">UGX <?php echo number_format($receipt['amount_paid'], 2); ?></td>
        </tr>
        <tr>
            <td class="label">Balance Remaining:</td>
            <td class="value balance-highlight">UGX <?php echo number_format($receipt['balance'], 2); ?></td>
        </tr>
        <tr>
            <td class="label">Payment Date:</td>
            <td class="value"><?php echo date('F d, Y', strtotime($receipt['payment_date'])); ?></td>
        </tr>
        <tr>
            <td class="label">Recorded By:</td>
            <td class="value"><?php echo htmlspecialchars($receipt['recorded_by'] ?? 'Bursar'); ?></td>
        </tr>
    </table>

    <div class="footer-note">
        <p>Thank you for your payment!</p>
    </div>
</div>

</body>
</html>