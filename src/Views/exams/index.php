<?php
$avgScore = $exam_marks ? array_sum(array_column($exam_marks, 'score')) / count($exam_marks) : null;
?>
<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Exams & Marks Management</h2>
            <p class="text-muted">Create exams and record student scores per subject.</p>
        </div>
    </div>
</div>

<div class="row column1">
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-file-text red_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo count($exams); ?></p>
                    <p class="head_couter">Total Exams</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-list-alt blue1_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo count($exam_marks); ?></p>
                    <p class="head_couter"><?php echo $selected_exam ? 'Marks Recorded' : 'Select an Exam'; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-line-chart green_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $avgScore !== null ? number_format($avgScore, 1) : '-'; ?></p>
                    <p class="head_couter">Average Score</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0 d-flex justify-content-between align-items-center flex-wrap">
                    <h2>Exams</h2>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#examModal">
                        <i class="fa fa-plus"></i> Create Exam
                    </button>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <table class="table table-bordered table-striped align-middle">
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
                            <tr class="<?php echo ($selected_exam && $selected_exam['id'] == $exam['id']) ? 'table-active' : ''; ?>">
                                <td><strong><?php echo htmlspecialchars($exam['exam_name']); ?></strong></td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($exam['term'] ?? 'N/A'); ?></span></td>
                                <td><?php echo htmlspecialchars($exam['year'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($exam['class'] ?? 'All'); ?></td>
                                <td class="text-nowrap">
                                    <a href="/dashboard/exams?exam_id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-info" title="View Marks">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this exam? All marks will be deleted too.');">
                                        <input type="hidden" name="action" value="delete_exam">
                                        <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
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

    <div class="col-md-6">
        <?php if ($selected_exam): ?>
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0 d-flex justify-content-between align-items-center flex-wrap">
                    <h2>Marks for <?php echo htmlspecialchars($selected_exam['exam_name']); ?></h2>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#marksModal">
                        <i class="fa fa-plus"></i> Add Marks
                    </button>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <table class="table table-bordered table-striped table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th class="text-right">Score</th>
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
                                <td class="text-right">
                                    <span class="badge <?php echo $mark['score'] >= 50 ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo number_format($mark['score'], 2); ?>
                                    </span>
                                </td>
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
            <div class="table_section padding_infor_info text-center py-5">
                <i class="fa fa-file-text-o" style="font-size: 42px; color: #cbd3da;"></i>
                <p class="text-muted mt-3 mb-0">Click the <i class="fa fa-eye"></i> icon on an exam to view and manage its marks.</p>
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
                        <select class="form-control" name="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($exam_students as $student): ?>
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
