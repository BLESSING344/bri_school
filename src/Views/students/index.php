<?php
$maleCount = count(array_filter($students, fn($s) => ($s['gender'] ?? '') === 'Male'));
$femaleCount = count(array_filter($students, fn($s) => ($s['gender'] ?? '') === 'Female'));
?>
<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Students Management</h2>
            <p class="text-muted">Enroll students and keep their class and parent details up to date.</p>
        </div>
    </div>
</div>

<div class="row column1">
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-users blue1_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo count($students); ?></p>
                    <p class="head_couter">Total Students</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-male green_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $maleCount; ?></p>
                    <p class="head_couter">Male Students</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-female purple_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $femaleCount; ?></p>
                    <p class="head_couter">Female Students</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0 d-flex justify-content-between align-items-center flex-wrap">
                    <h2>Students List</h2>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#studentModal">
                        <i class="fa fa-plus"></i> Add New Student
                    </button>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <div class="table-responsive-sm">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Gender</th>
                                <th>Class</th>
                                <th>Parent Name</th>
                                <th>Parent Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No students found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($student['full_name']); ?></strong></td>
                                    <td>
                                        <span class="badge <?php echo $student['gender'] === 'Male' ? 'badge-info' : 'badge-secondary'; ?>">
                                            <i class="fa fa-<?php echo $student['gender'] === 'Male' ? 'male' : 'female'; ?>"></i> <?php echo htmlspecialchars($student['gender']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td><?php echo htmlspecialchars($student['parent_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($student['parent_contact'] ?? ''); ?></td>
                                    <td class="text-nowrap">
                                        <a href="/dashboard/students/report-card?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank" title="Report Card">
                                            <i class="fa fa-file-text"></i>
                                        </a>
                                        <a href="?edit=<?php echo $student['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#studentModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($student)); ?>)" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fa fa-trash"></i>
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

<!-- Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_student ? 'Edit Student' : 'Add New Student'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_student ? 'edit' : 'add'; ?>">
                    <?php if ($edit_student): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($edit_student['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Gender *</label>
                        <select class="form-control" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo (isset($edit_student['gender']) && $edit_student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($edit_student['gender']) && $edit_student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Class *</label>
                        <input type="text" class="form-control" name="class" value="<?php echo htmlspecialchars($edit_student['class'] ?? ''); ?>" list="classesList" required>
                        <datalist id="classesList">
                            <?php foreach ($classes_list as $class_name): ?>
                                <option value="<?php echo htmlspecialchars($class_name); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label>Parent Name</label>
                        <input type="text" class="form-control" name="parent_name" value="<?php echo htmlspecialchars($edit_student['parent_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Parent Contact</label>
                        <input type="text" class="form-control" name="parent_contact" value="<?php echo htmlspecialchars($edit_student['parent_contact'] ?? ''); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_student ? 'Update' : 'Add'; ?> Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadEditData(student) {
    document.querySelector('input[name="full_name"]').value = student.full_name || '';
    document.querySelector('select[name="gender"]').value = student.gender || '';
    document.querySelector('input[name="class"]').value = student.class || '';
    document.querySelector('input[name="parent_name"]').value = student.parent_name || '';
    document.querySelector('input[name="parent_contact"]').value = student.parent_contact || '';
    document.querySelector('input[name="action"]').value = 'edit';
    document.querySelector('input[name="id"]').value = student.id;
    document.querySelector('.modal-title').textContent = 'Edit Student';
}
</script>
