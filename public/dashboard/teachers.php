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
        if ($_POST['action'] == 'add') {
            $full_name = trim($_POST['full_name']);
            $subject = trim($_POST['subject'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $class_assigned = trim($_POST['class_assigned'] ?? '');
            
            $sql = "INSERT INTO teachers (full_name, subject, phone, email, class_assigned) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$full_name, $subject, $phone, $email, $class_assigned])) {
                header('Location: teachers.php?success=Teacher added successfully');
                exit();
            }
        }
        elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $full_name = trim($_POST['full_name']);
            $subject = trim($_POST['subject'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $class_assigned = trim($_POST['class_assigned'] ?? '');
            
            $sql = "UPDATE teachers SET full_name=?, subject=?, phone=?, email=?, class_assigned=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$full_name, $subject, $phone, $email, $class_assigned, $id])) {
                header('Location: teachers.php?success=Teacher updated successfully');
                exit();
            }
        }
        elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];
            $sql = "DELETE FROM teachers WHERE id=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                header('Location: teachers.php?success=Teacher deleted successfully');
                exit();
            }
        }
        elseif ($_POST['action'] == 'import_from_user') {
            $user_id = $_POST['user_id'];
            $full_name = trim($_POST['full_name']);
            
            // Check if teacher already exists
            $check_sql = "SELECT id FROM teachers WHERE full_name = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$full_name]);
            
            if (!$check_stmt->fetch()) {
                // Import teacher from user
                $sql = "INSERT INTO teachers (full_name) VALUES (?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$full_name])) {
                    header('Location: teachers.php?success=Teacher imported from user successfully');
                    exit();
                }
            } else {
                header('Location: teachers.php?error=Teacher already exists');
                exit();
            }
        }
    }
}

// Get all teachers
try {
    $sql = "SELECT * FROM teachers ORDER BY full_name";
    $teachers = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
    error_log("Teachers query error: " . $e->getMessage());
}

// Get users with teacher role that aren't in teachers table yet
try {
    $sql_unlinked = "SELECT u.id, u.username, u.full_name 
                     FROM users u 
                     LEFT JOIN teachers t ON u.full_name = t.full_name 
                     WHERE u.role = 'teacher' AND t.id IS NULL 
                     ORDER BY u.full_name";
    $unlinked_teachers = $pdo->query($sql_unlinked)->fetchAll();
} catch (PDOException $e) {
    $unlinked_teachers = [];
}

// Get all classes for dropdown
try {
    $sql_classes = "SELECT DISTINCT class_name FROM classes ORDER BY class_name";
    $classes_list = $pdo->query($sql_classes)->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $classes_list = [];
}

// Get teacher for editing
$edit_teacher = null;
if (isset($_GET['edit'])) {
    try {
        $sql = "SELECT * FROM teachers WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['edit']]);
        $edit_teacher = $stmt->fetch();
    } catch (PDOException $e) {
        $edit_teacher = null;
    }
}

// Now include header after POST handling is done
require_once __DIR__ . '/includes/header.php';

$page_title = 'Teachers Management';
?>

<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Teachers Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Teachers List</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <div class="table-responsive-sm">
                    <?php if (!empty($unlinked_teachers)): ?>
                    <div class="alert alert-info mb-3">
                        <strong><i class="fa fa-info-circle"></i> Found <?php echo count($unlinked_teachers); ?> user(s) with teacher role that need to be added to teachers list:</strong>
                        <ul class="mb-2 mt-2">
                            <?php foreach ($unlinked_teachers as $unlinked): ?>
                            <li>
                                <?php echo htmlspecialchars($unlinked['full_name']); ?> (<?php echo htmlspecialchars($unlinked['username']); ?>)
                                <form method="POST" style="display:inline;" class="d-inline">
                                    <input type="hidden" name="action" value="import_from_user">
                                    <input type="hidden" name="user_id" value="<?php echo $unlinked['id']; ?>">
                                    <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($unlinked['full_name']); ?>">
                                    <button type="submit" class="btn btn-sm btn-success ml-2">
                                        <i class="fa fa-plus"></i> Import to Teachers
                                    </button>
                                </form>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#teacherModal">
                        <i class="fa fa-plus"></i> Add New Teacher
                    </button>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Subject</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Class Assigned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($teachers)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No teachers found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo $teacher['id']; ?></td>
                                    <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['subject'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['phone'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['class_assigned'] ?? ''); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#teacherModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($teacher)); ?>)">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
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

<!-- Teacher Modal -->
<div class="modal fade" id="teacherModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_teacher ? 'Edit Teacher' : 'Add New Teacher'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_teacher ? 'edit' : 'add'; ?>">
                    <?php if ($edit_teacher): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_teacher['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($edit_teacher['full_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" name="subject" value="<?php echo htmlspecialchars($edit_teacher['subject'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($edit_teacher['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($edit_teacher['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Class Assigned</label>
                        <input type="text" class="form-control" name="class_assigned" value="<?php echo htmlspecialchars($edit_teacher['class_assigned'] ?? ''); ?>" list="classesList">
                        <datalist id="classesList">
                            <?php foreach ($classes_list as $class_name): ?>
                                <option value="<?php echo htmlspecialchars($class_name); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_teacher ? 'Update' : 'Add'; ?> Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadEditData(teacher) {
    document.querySelector('input[name="full_name"]').value = teacher.full_name || '';
    document.querySelector('input[name="subject"]').value = teacher.subject || '';
    document.querySelector('input[name="phone"]').value = teacher.phone || '';
    document.querySelector('input[name="email"]').value = teacher.email || '';
    document.querySelector('input[name="class_assigned"]').value = teacher.class_assigned || '';
    document.querySelector('input[name="action"]').value = 'edit';
    document.querySelector('input[name="id"]').value = teacher.id;
    document.querySelector('.modal-title').textContent = 'Edit Teacher';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
