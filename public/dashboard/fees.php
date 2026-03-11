<?php
// Require auth and database BEFORE any output (auth.php handles session_start)
require_once __DIR__ . '/includes/auth.php';

// Check authentication and role BEFORE any output
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Check role
$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'admin' && $user_role !== 'bursar') {
    header('Location: index.php?error=access_denied');
    exit();
}

// Handle form submissions BEFORE including header (to allow redirects)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'record_payment') {
            $student_id = $_POST['student_id'];
            $term = trim($_POST['term'] ?? '');
            $amount_paid = $_POST['amount_paid'];
            $balance = $_POST['balance'] ?? 0;
            $recorded_by = $_SESSION['full_name'];
            
            $sql = "INSERT INTO fees (student_id, term, amount_paid, balance, recorded_by) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$student_id, $term, $amount_paid, $balance, $recorded_by])) {
                header('Location: fees.php?success=Payment recorded successfully');
                exit();
            }
        }
        elseif ($_POST['action'] == 'update_payment') {
            $id = $_POST['id'];
            $term = trim($_POST['term'] ?? '');
            $amount_paid = $_POST['amount_paid'];
            $balance = $_POST['balance'] ?? 0;
            
            $sql = "UPDATE fees SET term=?, amount_paid=?, balance=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$term, $amount_paid, $balance, $id])) {
                header('Location: fees.php?success=Payment updated successfully');
                exit();
            }
        }
        elseif ($_POST['action'] == 'delete_payment') {
            $id = $_POST['id'];
            $sql = "DELETE FROM fees WHERE id=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                header('Location: fees.php?success=Payment record deleted successfully');
                exit();
            }
        }
    }
}

// Get filter parameters
$filter_student = $_GET['student'] ?? '';
$filter_term = $_GET['term'] ?? '';

// Get all students
$sql_students = "SELECT id, full_name, class FROM students ORDER BY full_name";
$students_list = $pdo->query($sql_students)->fetchAll();

// Get fees records
$sql = "SELECT f.*, s.full_name as student_name, s.class 
        FROM fees f 
        JOIN students s ON f.student_id = s.id 
        WHERE 1=1";
$params = [];

if ($filter_student) {
    $sql .= " AND f.student_id = ?";
    $params[] = $filter_student;
}

if ($filter_term) {
    $sql .= " AND f.term = ?";
    $params[] = $filter_term;
}

$sql .= " ORDER BY f.payment_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$fees_records = $stmt->fetchAll();

// Calculate summary
$sql_summary = "SELECT 
                    SUM(amount_paid) as total_paid,
                    SUM(balance) as total_balance,
                    COUNT(*) as total_records
                FROM fees WHERE 1=1";
$summary_params = [];
if ($filter_student) {
    $sql_summary .= " AND student_id = ?";
    $summary_params[] = $filter_student;
}
if ($filter_term) {
    $sql_summary .= " AND term = ?";
    $summary_params[] = $filter_term;
}
$summary_stmt = $pdo->prepare($sql_summary);
$summary_stmt->execute($summary_params);
$summary = $summary_stmt->fetch();

// Now include header after POST handling is done
require_once __DIR__ . '/includes/header.php';

$page_title = 'Fees Management';
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
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Fees Records</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <!-- Summary Cards -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>Total Paid</h5>
                                <h3><?php echo number_format($summary['total_paid'] ?? 0, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5>Total Balance</h5>
                                <h3><?php echo number_format($summary['total_balance'] ?? 0, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5>Total Records</h5>
                                <h3><?php echo $summary['total_records'] ?? 0; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Student</label>
                                <select class="form-control" name="student">
                                    <option value="">All Students</option>
                                    <?php foreach ($students_list as $student): ?>
                                        <option value="<?php echo $student['id']; ?>" <?php echo $filter_student == $student['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($student['full_name']); ?> (<?php echo htmlspecialchars($student['class']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Term</label>
                                <select class="form-control" name="term">
                                    <option value="">All Terms</option>
                                    <option value="Term 1" <?php echo $filter_term == 'Term 1' ? 'selected' : ''; ?>>Term 1</option>
                                    <option value="Term 2" <?php echo $filter_term == 'Term 2' ? 'selected' : ''; ?>>Term 2</option>
                                    <option value="Term 3" <?php echo $filter_term == 'Term 3' ? 'selected' : ''; ?>>Term 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#paymentModal">
                    <i class="fa fa-plus"></i> Record Payment
                </button>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Payment Date</th>
                            <th>Recorded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fees_records)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No payment records found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($fees_records as $record): ?>
                            <tr>
                                <td><?php echo $record['id']; ?></td>
                                <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['class']); ?></td>
                                <td><?php echo htmlspecialchars($record['term'] ?? 'N/A'); ?></td>
                                <td><?php echo number_format($record['amount_paid'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $record['balance'] > 0 ? 'badge-danger' : 'badge-success'; ?>">
                                        <?php echo number_format($record['balance'], 2); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($record['payment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($record['recorded_by'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $record['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#paymentModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($record)); ?>)">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this payment record?');">
                                        <input type="hidden" name="action" value="delete_payment">
                                        <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php
                    $edit_payment = null;
                    if (isset($_GET['edit'])) {
                        $sql = "SELECT * FROM fees WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$_GET['edit']]);
                        $edit_payment = $stmt->fetch();
                    }
                    ?>
                    <input type="hidden" name="action" value="<?php echo $edit_payment ? 'update_payment' : 'record_payment'; ?>">
                    <?php if ($edit_payment): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_payment['id']; ?>">
                    <?php endif; ?>
                    
                    <?php if (!$edit_payment): ?>
                    <div class="form-group">
                        <label>Student *</label>
                        <select class="form-control" name="student_id" id="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students_list as $student): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['full_name']); ?> (<?php echo htmlspecialchars($student['class']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="student_id" value="<?php echo $edit_payment['student_id']; ?>">
                        <?php
                        $std_sql = "SELECT full_name, class FROM students WHERE id = ?";
                        $std_stmt = $pdo->prepare($std_sql);
                        $std_stmt->execute([$edit_payment['student_id']]);
                        $student_info = $std_stmt->fetch();
                        ?>
                        <div class="form-group">
                            <label>Student</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_info['full_name']); ?> (<?php echo htmlspecialchars($student_info['class']); ?>)" readonly>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Term</label>
                        <select class="form-control" name="term">
                            <option value="">Select Term</option>
                            <option value="Term 1" <?php echo (isset($edit_payment['term']) && $edit_payment['term'] == 'Term 1') ? 'selected' : ''; ?>>Term 1</option>
                            <option value="Term 2" <?php echo (isset($edit_payment['term']) && $edit_payment['term'] == 'Term 2') ? 'selected' : ''; ?>>Term 2</option>
                            <option value="Term 3" <?php echo (isset($edit_payment['term']) && $edit_payment['term'] == 'Term 3') ? 'selected' : ''; ?>>Term 3</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Amount Paid *</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount_paid" value="<?php echo htmlspecialchars($edit_payment['amount_paid'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Balance</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="balance" value="<?php echo htmlspecialchars($edit_payment['balance'] ?? '0'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><?php echo $edit_payment ? 'Update' : 'Record'; ?> Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadEditData(payment) {
    document.querySelector('select[name="term"]').value = payment.term || '';
    document.querySelector('input[name="amount_paid"]').value = payment.amount_paid || '';
    document.querySelector('input[name="balance"]').value = payment.balance || '0';
    document.querySelector('input[name="action"]').value = 'update_payment';
    document.querySelector('input[name="id"]').value = payment.id;
    document.querySelector('.modal-title').textContent = 'Edit Payment Record';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
