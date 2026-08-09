<?php
$assignedCount = count(array_filter($teachers, fn($t) => !empty($t['class_assigned'])));
$unassignedCount = count($teachers) - $assignedCount;
?>
<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Teachers Management</h2>
            <p class="text-muted">Manage teaching staff and their class assignments.</p>
        </div>
    </div>
</div>

<div class="row column1">
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-user green_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo count($teachers); ?></p>
                    <p class="head_couter">Total Teachers</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-check-square blue1_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $assignedCount; ?></p>
                    <p class="head_couter">Assigned to a Class</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-question-circle orange_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $unassignedCount; ?></p>
                    <p class="head_couter">Unassigned</p>
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
                    <h2>Teachers List</h2>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#teacherModal">
                        <i class="fa fa-plus"></i> Add New Teacher
                    </button>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <?php if (!empty($unlinked_teachers)): ?>
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="fa fa-info-circle mt-1 mr-2"></i>
                    <div>
                        <strong><?php echo count($unlinked_teachers); ?> user account(s)</strong> have the teacher role but aren't in the teachers list yet:
                        <ul class="mb-0 mt-2 pl-3">
                            <?php foreach ($unlinked_teachers as $unlinked): ?>
                            <li class="mb-1">
                                <?php echo htmlspecialchars($unlinked['full_name']); ?> (<?php echo htmlspecialchars($unlinked['username']); ?>)
                                <form method="POST" style="display:inline;" class="d-inline">
                                    <input type="hidden" name="action" value="import_from_user">
                                    <input type="hidden" name="user_id" value="<?php echo $unlinked['id']; ?>">
                                    <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($unlinked['full_name']); ?>">
                                    <button type="submit" class="btn btn-sm btn-success ml-2">
                                        <i class="fa fa-plus"></i> Import
                                    </button>
                                </form>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <div class="table-responsive-sm">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
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
                                    <td colspan="6" class="text-center">No teachers found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($teacher['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($teacher['subject'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['phone'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email'] ?? ''); ?></td>
                                    <td>
                                        <?php if (!empty($teacher['class_assigned'])): ?>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($teacher['class_assigned']); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-light text-muted">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="?edit=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#teacherModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($teacher)); ?>)" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
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
