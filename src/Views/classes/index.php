<?php
$totalEnrolled = array_sum(array_column($classes, 'student_count'));
$unassignedClasses = count(array_filter($classes, fn($c) => empty($c['teacher_in_charge'])));
?>
<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Classes Management</h2>
            <p class="text-muted">Organize classes and assign a teacher in charge of each one.</p>
        </div>
    </div>
</div>

<div class="row column1">
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-building orange_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo count($classes); ?></p>
                    <p class="head_couter">Total Classes</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-users blue1_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $totalEnrolled; ?></p>
                    <p class="head_couter">Total Enrolled Students</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-question-circle red_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $unassignedClasses; ?></p>
                    <p class="head_couter">Without a Teacher</p>
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
                    <h2>Classes List</h2>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#classModal">
                        <i class="fa fa-plus"></i> Add New Class
                    </button>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <div class="table-responsive-sm">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Teacher in Charge</th>
                                <th>Students Enrolled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classes)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No classes found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($class['class_name']); ?></strong></td>
                                    <td>
                                        <?php if (!empty($class['teacher_in_charge'])): ?>
                                            <?php echo htmlspecialchars($class['teacher_in_charge']); ?>
                                        <?php else: ?>
                                            <span class="badge badge-light text-muted">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-secondary"><?php echo $class['student_count']; ?></span></td>
                                    <td class="text-nowrap">
                                        <a href="?edit=<?php echo $class['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#classModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($class)); ?>)" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this class?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
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
    document.querySelector('select[name="teacher_in_charge"]').value = cls.teacher_in_charge || '';
    document.querySelector('input[name="action"]').value = 'edit';
    document.querySelector('input[name="id"]').value = cls.id;
    document.querySelector('.modal-title').textContent = 'Edit Class';
}
</script>
