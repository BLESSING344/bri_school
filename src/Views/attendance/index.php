<?php
$presentCount = count(array_filter($attendance_records, fn($r) => $r['status'] === 'Present'));
$absentCount = count($attendance_records) - $presentCount;
?>
<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Attendance Management</h2>
            <p class="text-muted">Mark daily attendance and review records by class.</p>
        </div>
    </div>
</div>

<div class="row column1">
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-check-circle green_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $presentCount; ?></p>
                    <p class="head_couter">Present on <?php echo date('M j', strtotime($filter_date)); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div><i class="fa fa-times-circle red_color"></i></div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $absentCount; ?></p>
                    <p class="head_couter">Absent on <?php echo date('M j', strtotime($filter_date)); ?></p>
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
                    <p class="total_no"><?php echo count($attendance_records); ?></p>
                    <p class="head_couter">Total Marked</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Filter</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <form method="GET" action="/dashboard/attendance">
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
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Bulk Mark Attendance by Class</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <form method="POST" action="/dashboard/attendance">
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
                                <button type="button" class="btn btn-info form-control" onclick="loadStudentsForAttendance(document.getElementById('bulk_class').value)">
                                    <i class="fa fa-refresh"></i> Load Students
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="students_list"></div>
                    <button type="submit" class="btn btn-success mt-2">
                        <i class="fa fa-save"></i> Save Attendance
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Attendance Records for <?php echo date('F j, Y', strtotime($filter_date)); ?></h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <div class="table-responsive-sm">
                    <table class="table table-bordered table-striped align-middle">
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
                                            <i class="fa fa-<?php echo $record['status'] == 'Present' ? 'check-circle' : 'times-circle'; ?>"></i>
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

<script>
function loadStudentsForAttendance(class_name) {
    if (!class_name) {
        document.getElementById('students_list').innerHTML = '<p class="text-warning">Please select a class first</p>';
        return;
    }

    document.getElementById('students_list').innerHTML = '<p class="text-info"><i class="fa fa-spinner fa-spin"></i> Loading students...</p>';

    fetch('/dashboard/attendance/ajax?action=get_students&class=' + encodeURIComponent(class_name))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.students && data.students.length > 0) {
                    let html = '<table class="table table-bordered table-sm mt-2"><thead><tr><th>Student Name</th><th>Status</th></tr></thead><tbody>';
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
