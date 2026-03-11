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
if ($user_role !== 'admin' && $user_role !== 'teacher') {
    header('Location: index.php?error=access_denied');
    exit();
}

// Handle form submissions BEFORE including header (to allow redirects)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'mark') {
            $student_id = $_POST['student_id'];
            $date = $_POST['date'];
            $status = $_POST['status'];
            $recorded_by = $_SESSION['full_name'];
            
            // Check if attendance already exists for this student on this date
            $check_sql = "SELECT id FROM attendance WHERE student_id = ? AND date = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$student_id, $date]);
            $existing = $check_stmt->fetch();
            
            if ($existing) {
                // Update existing record
                $sql = "UPDATE attendance SET status=?, recorded_by=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$status, $recorded_by, $existing['id']]);
            } else {
                // Insert new record
                $sql = "INSERT INTO attendance (student_id, date, status, recorded_by) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$student_id, $date, $status, $recorded_by]);
            }
            
            header('Location: attendance.php?success=Attendance marked successfully');
            exit();
        }
        elseif ($_POST['action'] == 'bulk_mark') {
            $date = $_POST['bulk_date'];
            $class = $_POST['bulk_class'];
            $recorded_by = $_SESSION['full_name'];
            
            // Get all students in the class
            $students_sql = "SELECT id FROM students WHERE class = ?";
            $students_stmt = $pdo->prepare($students_sql);
            $students_stmt->execute([$class]);
            $students = $students_stmt->fetchAll();
            
            $count = 0;
            foreach ($students as $student) {
                $status = $_POST['attendance_' . $student['id']] ?? 'Absent';
                
                // Check if exists
                $check_sql = "SELECT id FROM attendance WHERE student_id = ? AND date = ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$student['id'], $date]);
                $existing = $check_stmt->fetch();
                
                if ($existing) {
                    $sql = "UPDATE attendance SET status=?, recorded_by=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$status, $recorded_by, $existing['id']]);
                } else {
                    $sql = "INSERT INTO attendance (student_id, date, status, recorded_by) VALUES (?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$student['id'], $date, $status, $recorded_by]);
                }
                $count++;
            }
            
            header('Location: attendance.php?success=Bulk attendance marked successfully for ' . $count . ' students');
            exit();
        }
    }
}

// Get filter parameters
$filter_date = $_GET['date'] ?? date('Y-m-d');
$filter_class = $_GET['class'] ?? '';

// Get all classes
$sql_classes = "SELECT DISTINCT class_name FROM classes ORDER BY class_name";
$classes_list = $pdo->query($sql_classes)->fetchAll(PDO::FETCH_COLUMN);

// Get attendance records for the selected date
$attendance_records = [];
if ($filter_date) {
    $sql = "SELECT a.*, s.full_name as student_name, s.class 
            FROM attendance a 
            JOIN students s ON a.student_id = s.id 
            WHERE a.date = ?";
    $params = [$filter_date];
    
    if ($filter_class) {
        $sql .= " AND s.class = ?";
        $params[] = $filter_class;
    }
    
    $sql .= " ORDER BY s.class, s.full_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $attendance_records = $stmt->fetchAll();
}

// Now include header after POST handling is done
require_once __DIR__ . '/includes/header.php';

$page_title = 'Attendance Management';
?>

<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Attendance Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Mark Attendance</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($filter_date); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Class (Optional)</label>
                                <select class="form-control" name="class">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes_list as $class_name): ?>
                                        <option value="<?php echo htmlspecialchars($class_name); ?>" <?php echo $filter_class == $class_name ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($class_name); ?>
                                        </option>
                                    <?php endforeach; ?>
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

                <!-- Bulk Mark Attendance -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Bulk Mark Attendance by Class</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="bulk_mark">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date *</label>
                                        <input type="date" class="form-control" name="bulk_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Class *</label>
                                        <select class="form-control" name="bulk_class" id="bulk_class" required onchange="loadStudentsForAttendance(this.value)">
                                            <option value="">Select Class</option>
                                            <?php foreach ($classes_list as $class_name): ?>
                                                <option value="<?php echo htmlspecialchars($class_name); ?>">
                                                    <?php echo htmlspecialchars($class_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-info form-control" onclick="loadStudentsForAttendance(document.getElementById('bulk_class').value)">Load Students</button>
                                    </div>
                                </div>
                            </div>
                            <div id="students_list"></div>
                            <button type="submit" class="btn btn-success mt-2">Save Attendance</button>
                        </form>
                    </div>
                </div>

                <!-- Attendance Records -->
                <div class="card">
                    <div class="card-header">
                        <h5>Attendance Records for <?php echo date('F j, Y', strtotime($filter_date)); ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Status</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($attendance_records)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No attendance records for this date</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attendance_records as $record): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($record['class']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $record['status'] == 'Present' ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo htmlspecialchars($record['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($record['recorded_by'] ?? 'N/A'); ?></td>
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
</div>

<script>
function loadStudentsForAttendance(class_name) {
    if (!class_name) {
        document.getElementById('students_list').innerHTML = '<p class="text-warning">Please select a class first</p>';
        return;
    }
    
    // Show loading message
    document.getElementById('students_list').innerHTML = '<p class="text-info"><i class="fa fa-spinner fa-spin"></i> Loading students...</p>';
    
    fetch('attendance_ajax.php?action=get_students&class=' + encodeURIComponent(class_name))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.students && data.students.length > 0) {
                    let html = '<table class="table table-bordered table-sm"><thead><tr><th>Student Name</th><th>Status</th></tr></thead><tbody>';
                    data.students.forEach(student => {
                        html += `<tr>
                            <td>${student.full_name}</td>
                            <td>
                                <select name="attendance_${student.id}" class="form-control">
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                </select>
                            </td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('students_list').innerHTML = html;
                } else {
                    document.getElementById('students_list').innerHTML = '<p class="text-warning">No students found in this class</p>';
                }
            } else {
                document.getElementById('students_list').innerHTML = '<p class="text-danger">' + (data.message || 'Error loading students') + '</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('students_list').innerHTML = '<p class="text-danger">Error loading students: ' + error.message + '</p>';
        });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
