<?php
<<<<<<< Updated upstream

require_once __DIR__ . '/../app/bootstrap.php';

(new App\Controllers\FeesController())->index();
=======
require_once __DIR__ . '/includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'admin' && $user_role !== 'bursar') {
    header('Location: index.php?error=access_denied');
    exit();
}

// Handle Form POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'record_payment') {
        $student_id = $_POST['student_id'];
        $term       = trim($_POST['term'] ?? '');
        $amount     = $_POST['amount_paid'];
        $balance    = $_POST['balance'] ?? 0;
        $recorded   = $_SESSION['full_name'] ?? 'Bursar';

        $stmt = $pdo->prepare("INSERT INTO fees (student_id, term, amount_paid, balance, recorded_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $term, $amount, $balance, $recorded]);

        header('Location: fees.php?success=Payment+recorded');
        exit();
    } 
    elseif ($_POST['action'] === 'update_payment') {
        $id      = $_POST['id'];
        $term    = trim($_POST['term'] ?? '');
        $amount  = $_POST['amount_paid'];
        $balance = $_POST['balance'] ?? 0;

        $stmt = $pdo->prepare("UPDATE fees SET term=?, amount_paid=?, balance=? WHERE id=?");
        $stmt->execute([$term, $amount, $balance, $id]);

        header('Location: fees.php?success=Payment+updated');
        exit();
    } 
    elseif ($_POST['action'] === 'delete_payment') {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM fees WHERE id=?");
        $stmt->execute([$id]);

        header('Location: fees.php?success=Payment+deleted');
        exit();
    }
}

$filter_student = $_GET['student'] ?? '';
$filter_term    = $_GET['term'] ?? '';

$students_list = $pdo->query("SELECT id, full_name, class FROM students ORDER BY full_name")->fetchAll();

$sql = "SELECT f.*, s.full_name AS student_name, s.class 
        FROM fees f 
        JOIN students s ON f.student_id = s.id 
        WHERE 1=1";
$params = [];

if ($filter_student !== '') {
    $sql .= " AND f.student_id = ?";
    $params[] = $filter_student;
}
if ($filter_term !== '') {
    $sql .= " AND f.term = ?";
    $params[] = $filter_term;
}

$sql .= " ORDER BY f.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$fees_records = $stmt->fetchAll();

$summary_sql = "SELECT SUM(amount_paid) AS total_paid, SUM(balance) AS total_balance, COUNT(*) AS total_records FROM fees WHERE 1=1";
$summary_params = [];

if ($filter_student !== '') {
    $summary_sql .= " AND student_id = ?";
    $summary_params[] = $filter_student;
}
if ($filter_term !== '') {
    $summary_sql .= " AND term = ?";
    $summary_params[] = $filter_term;
}

$sum_stmt = $pdo->prepare($summary_sql);
$sum_stmt->execute($summary_params);
$summary = $sum_stmt->fetch();

require_once __DIR__ . '/includes/header.php';
?>

<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Fees Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="table_section padding_infor_info">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-success text-white p-3">
                            <h5>Total Paid</h5>
                            <h3>UGX <?php echo number_format($summary['total_paid'] ?? 0, 2); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white p-3">
                            <h5>Total Balance</h5>
                            <h3>UGX <?php echo number_format($summary['total_balance'] ?? 0, 2); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white p-3">
                            <h5>Total Records</h5>
                            <h3><?php echo $summary['total_records'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>

                <form method="GET" class="mb-4">
                    <div class="form-row">
                        <div class="col-md-5">
                            <select class="form-control" name="student">
                                <option value="">All Students</option>
                                <?php foreach ($students_list as $st): ?>
                                    <option value="<?php echo $st['id']; ?>" <?php echo ($filter_student == $st['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($st['full_name']); ?> (<?php echo htmlspecialchars($st['class']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select class="form-control" name="term">
                                <option value="">All Terms</option>
                                <option value="Term 1" <?php echo ($filter_term === 'Term 1') ? 'selected' : ''; ?>>Term 1</option>
                                <option value="Term 2" <?php echo ($filter_term === 'Term 2') ? 'selected' : ''; ?>>Term 2</option>
                                <option value="Term 3" <?php echo ($filter_term === 'Term 3') ? 'selected' : ''; ?>>Term 3</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                </form>

                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#paymentModal" onclick="resetForm()">
                    <i class="fa fa-plus"></i> Record Payment
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Term</th>
                                <th>Amount Paid</th>
                                <th>Balance</th>
                                <th>Date</th>
                                <th>Recorded By</th>
                                <th style="min-width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($fees_records)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">No payment records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($fees_records as $row): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                                    <td><?php echo htmlspecialchars($row['term'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($row['amount_paid'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($row['balance'] > 0) ? 'badge-danger' : 'badge-success'; ?>">
                                            <?php echo number_format($row['balance'], 2); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['recorded_by'] ?? 'N/A'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#paymentModal" onclick='fillEditForm(<?php echo json_encode($row); ?>)'>
                                            <i class="fa fa-edit"></i> Edit
                                        </button>

                                        <a href="generate_reciept.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fa fa-print"></i> Receipt
                                        </a>

                                        <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this payment record?');">
                                            <input type="hidden" name="action" value="delete_payment">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Record Payment</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" id="form_action" value="record_payment">
                    <input type="hidden" name="id" id="payment_id" value="">

                    <div class="form-group" id="studentSelectGroup">
                        <label>Student *</label>
                        <select class="form-control" name="student_id" id="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students_list as $st): ?>
                                <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['full_name']); ?> (<?php echo htmlspecialchars($st['class']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Term *</label>
                        <select class="form-control" name="term" id="term" required>
                            <option value="">Select Term</option>
                            <option value="Term 1">Term 1</option>
                            <option value="Term 2">Term 2</option>
                            <option value="Term 3">Term 3</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Amount Paid *</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount_paid" id="amount_paid" required>
                    </div>

                    <div class="form-group">
                        <label>Balance Remaining</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="balance" id="balance" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="submitBtn">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').innerText = 'Record Payment';
    document.getElementById('form_action').value = 'record_payment';
    document.getElementById('payment_id').value = '';
    document.getElementById('studentSelectGroup').style.display = 'block';
    document.getElementById('student_id').required = true;
    document.getElementById('term').value = '';
    document.getElementById('amount_paid').value = '';
    document.getElementById('balance').value = '0';
    document.getElementById('submitBtn').innerText = 'Save Payment';
}

function fillEditForm(data) {
    document.getElementById('modalTitle').innerText = 'Edit Payment Record #' + data.id;
    document.getElementById('form_action').value = 'update_payment';
    document.getElementById('payment_id').value = data.id;
    document.getElementById('studentSelectGroup').style.display = 'none';
    document.getElementById('student_id').required = false;
    document.getElementById('term').value = data.term || '';
    document.getElementById('amount_paid').value = data.amount_paid || '';
    document.getElementById('balance').value = data.balance || '0';
    document.getElementById('submitBtn').innerText = 'Update Payment';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
>>>>>>> Stashed changes
