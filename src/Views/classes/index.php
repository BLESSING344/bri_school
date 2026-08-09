<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Classes Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Classes List</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <div class="table-responsive-sm">
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#classModal">
                        <i class="fa fa-plus"></i> Add New Class
                    </button>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Class Name</th>
                                <th>Teacher in Charge</th>
                                <th>Number of Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classes)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No classes found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo $class['id']; ?></td>
                                    <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['teacher_in_charge'] ?? 'Not assigned'); ?></td>
                                    <td><?php echo $class['student_count']; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $class['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#classModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($class)); ?>)">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this class?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
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

<!-- Class Modal -->
<div class="modal fade" id="classModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_class ? 'Edit Class' : 'Add New Class'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_class ? 'edit' : 'add'; ?>">
                    <?php if ($edit_class): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_class['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Class Name *</label>
                        <input type="text" class="form-control" name="class_name" value="<?php echo htmlspecialchars($edit_class['class_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Teacher in Charge</label>
                        <select class="form-control" name="teacher_in_charge">
                            <option value="">-- Select Teacher --</option>
                            <?php foreach ($teachers_list as $teacher_name): ?>
                                <option value="<?php echo htmlspecialchars($teacher_name); ?>"
                                    <?php echo (isset($edit_class['teacher_in_charge']) && $edit_class['teacher_in_charge'] == $teacher_name) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($teacher_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($teachers_list)): ?>
                            <small class="text-muted">No teachers found. <a href="/dashboard/teachers">Add teachers first</a></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_class ? 'Update' : 'Add'; ?> Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadEditData(cls) {
    document.querySelector('input[name="class_name"]').value = cls.class_name || '';
    document.querySelector('input[name="teacher_in_charge"]').value = cls.teacher_in_charge || '';
    document.querySelector('input[name="action"]').value = 'edit';
    document.querySelector('input[name="id"]').value = cls.id;
    document.querySelector('.modal-title').textContent = 'Edit Class';
}
</script>
