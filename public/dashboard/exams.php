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
        if ($_POST['action'] == 'add_exam') {
            $exam_name = trim($_POST['exam_name']);
            $term = trim($_POST['term'] ?? '');
            $year = $_POST['year'] ?? date('Y');
            $class = trim($_POST['class'] ?? '');
            
            $sql = "INSERT INTO exams (exam_name, term, year, class) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$exam_name, $term, $year, $class])) {
                header('Location: exams.php?success=Exam created successfully');
                exit();
            }
        }
        elseif ($_POST['action'] == 'add_marks') {
            $exam_id = $_POST['exam_id'];
            $student_id = $_POST['student_id'];
            $subject = trim($_POST['subject']);
            $score = $_POST['score'];
            
            // Check if marks already exist
            $check_sql = "SELECT id FROM marks WHERE student_id = ? AND exam_id = ? AND subject = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$student_id, $exam_id, $subject]);
            $existing = $check_stmt->fetch();
            
            if ($existing) {
                $sql = "UPDATE marks SET score=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$score, $existing['id']]);
            } else {
                $sql = "INSERT INTO marks (student_id, exam_id, subject, score) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$student_id, $exam_id, $subject, $score]);
            }
            
            header('Location: exams.php?success=Marks added successfully');
            exit();
        }
        elseif ($_POST['action'] == 'delete_exam') {
            $id = $_POST['id'];
            $sql = "DELETE FROM exams WHERE id=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                header('Location: exams.php?success=Exam deleted successfully');
                exit();
            }
        }
    }
}

// Get all exams
$sql = "SELECT * FROM exams ORDER BY year DESC, term, exam_name";
$exams = $pdo->query($sql)->fetchAll();

// Get all classes
$sql_classes = "SELECT DISTINCT class_name FROM classes ORDER BY class_name";
$classes_list = $pdo->query($sql_classes)->fetchAll(PDO::FETCH_COLUMN);

// Get selected exam and its marks
$selected_exam = null;
$exam_marks = [];
if (isset($_GET['exam_id'])) {
    $sql = "SELECT * FROM exams WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_GET['exam_id']]);
    $selected_exam = $stmt->fetch();
    
    if ($selected_exam) {
        $sql = "SELECT m.*, s.full_name as student_name, s.class 
                FROM marks m 
                JOIN students s ON m.student_id = s.id 
                WHERE m.exam_id = ? 
                ORDER BY s.class, s.full_name, m.subject";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['exam_id']]);
        $exam_marks = $stmt->fetchAll();
    }
}

// Now include header after POST handling is done
require_once __DIR__ . '/includes/header.php';

$page_title = 'Exams & Marks Management';
?>

<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Exams & Marks Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Exams</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#examModal">
                    <i class="fa fa-plus"></i> Create New Exam
                </button>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Term</th>
                            <th>Year</th>
                            <th>Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($exams)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No exams found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($exams as $exam): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($exam['exam_name']); ?></td>
                                <td><?php echo htmlspecialchars($exam['term'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($exam['year'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($exam['class'] ?? 'All'); ?></td>
                                <td>
                                    <a href="?exam_id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i> View Marks
                                    </a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this exam? All marks will be deleted too.');">
                                        <input type="hidden" name="action" value="delete_exam">
                                        <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
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
    
    <div class="col-md-6">
        <?php if ($selected_exam): ?>
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Marks for <?php echo htmlspecialchars($selected_exam['exam_name']); ?></h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#marksModal">
                    <i class="fa fa-plus"></i> Add Marks
                </button>
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($exam_marks)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No marks recorded yet</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($exam_marks as $mark): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mark['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($mark['class']); ?></td>
                                <td><?php echo htmlspecialchars($mark['subject']); ?></td>
                                <td><?php echo number_format($mark['score'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Select an Exam</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <p class="text-muted">Click "View Marks" on an exam to see and manage marks.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Exam Modal -->
<div class="modal fade" id="examModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Exam</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_exam">
                    
                    <div class="form-group">
                        <label>Exam Name *</label>
                        <input type="text" class="form-control" name="exam_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Term</label>
                        <select class="form-control" name="term">
                            <option value="">Select Term</option>
                            <option value="Term 1">Term 1</option>
                            <option value="Term 2">Term 2</option>
                            <option value="Term 3">Term 3</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Year *</label>
                        <input type="number" class="form-control" name="year" value="<?php echo date('Y'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Class</label>
                        <input type="text" class="form-control" name="class" list="classesList">
                        <datalist id="classesList">
                            <?php foreach ($classes_list as $class_name): ?>
                                <option value="<?php echo htmlspecialchars($class_name); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Marks Modal -->
<?php if ($selected_exam): ?>
<div class="modal fade" id="marksModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Marks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_marks">
                    <input type="hidden" name="exam_id" value="<?php echo $selected_exam['id']; ?>">
                    
                    <div class="form-group">
                        <label>Student *</label>
                        <?php
                        $class_filter = $selected_exam['class'] ?? '';
                        $students_sql = "SELECT id, full_name FROM students";
                        $students_params = [];
                        if ($class_filter) {
                            $students_sql .= " WHERE class = ?";
                            $students_params[] = $class_filter;
                        }
                        $students_sql .= " ORDER BY full_name";
                        $students_stmt = $pdo->prepare($students_sql);
                        $students_stmt->execute($students_params);
                        $students = $students_stmt->fetchAll();
                        ?>
                        <select class="form-control" name="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Subject *</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Score *</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" name="score" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Marks</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
