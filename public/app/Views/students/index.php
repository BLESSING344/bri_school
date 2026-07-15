<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Students Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Students List</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <div class="table-responsive-sm">
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#studentModal">
                        <i class="fa fa-plus"></i> Add New Student
                    </button>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                    <td colspan="7" class="text-center">No students found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td><?php echo htmlspecialchars($student['parent_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($student['parent_contact'] ?? ''); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $student['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#studentModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($student)); ?>)">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
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
