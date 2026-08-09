<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Fees Management</h2>
            <p class="text-muted">Track payments, outstanding balances, and print receipts.</p>
        </div>
    </div>
</div>

<div class="row column1">
    <div class="col-md-6 col-lg-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div>
                    <i class="fa fa-money green_color"></i>
                </div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo number_format($summary['total_paid'] ?? 0, 2); ?></p>
                    <p class="head_couter">Total Paid</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div>
                    <i class="fa fa-exclamation-circle red_color"></i>
                </div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo number_format($summary['total_balance'] ?? 0, 2); ?></p>
                    <p class="head_couter">Outstanding Balance</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="full counter_section margin_bottom_30">
            <div class="couter_icon">
                <div>
                    <i class="fa fa-list-alt blue1_color"></i>
                </div>
            </div>
            <div class="counter_no">
                <div>
                    <p class="total_no"><?php echo $summary['total_records'] ?? 0; ?></p>
                    <p class="head_couter">Payment Records</p>
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
                    <h2>Fees Records</h2>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paymentModal">
                        <i class="fa fa-plus"></i> Record Payment
                    </button>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <!-- Filters -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Student</label>
                                <select class="form-control" name="student">
                                    <option value="">All Students</option>
                                    <?php foreach ($students_list as $student): ?>
                                        <option value="<?php echo $student['id']; ?>" <?php echo $filter_student == $student['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($student['full_name']); ?> (<?php echo htmlspecialchars($student['class']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Term</label>
                                <select class="form-control" name="term">
                                    <option value="">All Terms</option>
                                    <option value="Term 1" <?php echo $filter_term == 'Term 1' ? 'selected' : ''; ?>>Term 1</option>
                                    <option value="Term 2" <?php echo $filter_term == 'Term 2' ? 'selected' : ''; ?>>Term 2</option>
                                    <option value="Term 3" <?php echo $filter_term == 'Term 3' ? 'selected' : ''; ?>>Term 3</option>
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

                <div class="table-responsive-sm">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Term</th>
                                <th class="text-right">Amount Paid</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($fees_records)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No payment records found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($fees_records as $record): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($record['student_name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($record['class']); ?></td>
                                    <td><?php echo htmlspecialchars($record['term'] ?? 'N/A'); ?></td>
                                    <td class="text-right"><?php echo number_format($record['amount_paid'], 2); ?></td>
                                    <td>
                                        <?php if ($record['balance'] > 0): ?>
                                            <span class="badge badge-danger">
                                                <i class="fa fa-exclamation-circle"></i> <?php echo number_format($record['balance'], 2); ?> due
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success">
                                                <i class="fa fa-check-circle"></i> Paid in full
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($record['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['recorded_by'] ?? 'N/A'); ?></td>
                                    <td class="text-nowrap">
                                        <a href="/dashboard/fees/receipt?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank" title="View Receipt">
                                            <i class="fa fa-file-text"></i>
                                        </a>
                                        <a href="?edit=<?php echo $record['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#paymentModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($record)); ?>)" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this payment record?');">
                                            <input type="hidden" name="action" value="delete_payment">
                                            <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_payment ? 'Edit Payment Record' : 'Record Payment'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_payment ? 'update_payment' : 'record_payment'; ?>">
                    <?php if ($edit_payment): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_payment['id']; ?>">
                    <?php endif; ?>

                    <?php if (!$edit_payment): ?>
                    <div class="form-group">
                        <label>Student *</label>
                        <select class="form-control" name="student_id" id="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students_list as $student): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['full_name']); ?> (<?php echo htmlspecialchars($student['class']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="student_id" value="<?php echo $edit_payment['student_id']; ?>">
                        <div class="form-group">
                            <label>Student</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(($student_info['full_name'] ?? '') . ' (' . ($student_info['class'] ?? '') . ')'); ?>" readonly>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Term</label>
                        <select class="form-control" name="term">
                            <option value="">Select Term</option>
                            <option value="Term 1" <?php echo (isset($edit_payment['term']) && $edit_payment['term'] == 'Term 1') ? 'selected' : ''; ?>>Term 1</option>
                            <option value="Term 2" <?php echo (isset($edit_payment['term']) && $edit_payment['term'] == 'Term 2') ? 'selected' : ''; ?>>Term 2</option>
                            <option value="Term 3" <?php echo (isset($edit_payment['term']) && $edit_payment['term'] == 'Term 3') ? 'selected' : ''; ?>>Term 3</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Amount Paid *</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount_paid" value="<?php echo htmlspecialchars($edit_payment['amount_paid'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Balance</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="balance" value="<?php echo htmlspecialchars($edit_payment['balance'] ?? '0'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><?php echo $edit_payment ? 'Update' : 'Record'; ?> Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadEditData(payment) {
    document.querySelector('select[name="term"]').value = payment.term || '';
    document.querySelector('input[name="amount_paid"]').value = payment.amount_paid || '';
    document.querySelector('input[name="balance"]').value = payment.balance || '0';
    document.querySelector('input[name="action"]').value = 'update_payment';
    document.querySelector('input[name="id"]').value = payment.id;
    document.querySelector('.modal-title').textContent = 'Edit Payment Record';
}
</script>
