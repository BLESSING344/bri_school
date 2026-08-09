                     <div class="row column_title">
                        <div class="col-md-12">
                           <div class="page_title">
                              <h2>Dashboard</h2>
                              <p class="text-muted">Welcome back, <?php echo htmlspecialchars($current_user['full_name'] ?? 'User'); ?>! Here's an overview of your school management system.</p>
                           </div>
                        </div>
                     </div>

                     <div class="row column1">
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30">
                              <div class="couter_icon">
                                 <div>
                                    <i class="fa fa-users yellow_color"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?php echo $stats['students']; ?></p>
                                    <p class="head_couter">Total Students</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30">
                              <div class="couter_icon">
                                 <div>
                                    <i class="fa fa-user blue1_color"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?php echo $stats['teachers']; ?></p>
                                    <p class="head_couter">Total Teachers</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30">
                              <div class="couter_icon">
                                 <div>
                                    <i class="fa fa-building green_color"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?php echo $stats['classes']; ?></p>
                                    <p class="head_couter">Total Classes</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30">
                              <div class="couter_icon">
                                 <div>
                                    <i class="fa fa-check-square red_color"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?php echo $stats['attendance_today']; ?></p>
                                    <p class="head_couter">Today's Attendance</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <?php if ($is_admin || $is_bursar): ?>
                     <div class="row column1">
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30">
                              <div class="couter_icon">
                                 <div>
                                    <i class="fa fa-file-text purple_color"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?php echo $stats['exams']; ?></p>
                                    <p class="head_couter">Total Exams</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30">
                              <div class="couter_icon">
                                 <div>
                                    <i class="fa fa-money green_color"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?php echo number_format($stats['fees_collected'], 0); ?></p>
                                    <p class="head_couter">Fees Collected</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <?php endif; ?>
                     <div class="row column3">
                        <!-- Recent Students -->
                        <div class="col-md-6">
                           <div class="white_shd full margin_bottom_30">
                              <div class="full graph_head">
                                 <div class="heading1 margin_0">
                                    <h2>Recent Students</h2>
                                 </div>
                              </div>
                              <div class="full graph_revenue">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="content">
                                          <table class="table table-striped">
                                             <thead>
                                                <tr>
                                                   <th>Name</th>
                                                   <th>Class</th>
                                                   <th>Gender</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <?php if (empty($recent_students)): ?>
                                                   <tr>
                                                      <td colspan="3" class="text-center">No students found</td>
                                                   </tr>
                                                <?php else: ?>
                                                   <?php foreach ($recent_students as $student): ?>
                                                   <tr>
                                                      <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                                      <td><?php echo htmlspecialchars($student['class']); ?></td>
                                                      <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                                   </tr>
                                                   <?php endforeach; ?>
                                                <?php endif; ?>
                                             </tbody>
                                          </table>
                                          <div class="text-center mt-2">
                                             <a href="/dashboard/students.php" class="btn btn-primary btn-sm">View All Students</a>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Recent Attendance -->
                        <div class="col-md-6">
                           <div class="white_shd full margin_bottom_30">
                              <div class="full graph_head">
                                 <div class="heading1 margin_0">
                                    <h2>Recent Attendance</h2>
                                 </div>
                              </div>
                              <div class="full graph_revenue">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="content">
                                          <table class="table table-striped table-sm">
                                             <thead>
                                                <tr>
                                                   <th>Student</th>
                                                   <th>Class</th>
                                                   <th>Status</th>
                                                   <th>Date</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <?php if (empty($recent_attendance)): ?>
                                                   <tr>
                                                      <td colspan="4" class="text-center">No attendance records</td>
                                                   </tr>
                                                <?php else: ?>
                                                   <?php foreach ($recent_attendance as $att): ?>
                                                   <tr>
                                                      <td><?php echo htmlspecialchars($att['student_name']); ?></td>
                                                      <td><?php echo htmlspecialchars($att['class']); ?></td>
                                                      <td>
                                                         <span class="badge <?php echo $att['status'] == 'Present' ? 'badge-success' : 'badge-danger'; ?>">
                                                            <?php echo $att['status']; ?>
                                                         </span>
                                                      </td>
                                                      <td><?php echo date('M j', strtotime($att['date'])); ?></td>
                                                   </tr>
                                                   <?php endforeach; ?>
                                                <?php endif; ?>
                                             </tbody>
                                          </table>
                                          <div class="text-center mt-2">
                                             <a href="/dashboard/attendance.php" class="btn btn-primary btn-sm">View Attendance</a>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <?php if ($is_admin || $is_bursar): ?>
                     <div class="row column4">
                        <div class="col-md-12">
                           <div class="white_shd full margin_bottom_30">
                              <div class="full graph_head">
                                 <div class="heading1 margin_0">
                                    <h2>Recent Payments</h2>
                                 </div>
                              </div>
                              <div class="full graph_revenue">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="content">
                                          <table class="table table-striped">
                                             <thead>
                                                <tr>
                                                   <th>Student</th>
                                                   <th>Term</th>
                                                   <th>Amount Paid</th>
                                                   <th>Balance</th>
                                                   <th>Date</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <?php if (empty($recent_payments)): ?>
                                                   <tr>
                                                      <td colspan="5" class="text-center">No payment records</td>
                                                   </tr>
                                                <?php else: ?>
                                                   <?php foreach ($recent_payments as $payment): ?>
                                                   <tr>
                                                      <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                                                      <td><?php echo htmlspecialchars($payment['term'] ?? 'N/A'); ?></td>
                                                      <td><?php echo number_format($payment['amount_paid'], 2); ?></td>
                                                      <td>
                                                         <span class="badge <?php echo $payment['balance'] > 0 ? 'badge-danger' : 'badge-success'; ?>">
                                                            <?php echo number_format($payment['balance'], 2); ?>
                                                         </span>
                                                      </td>
                                                      <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                                                   </tr>
                                                   <?php endforeach; ?>
                                                <?php endif; ?>
                                             </tbody>
                                          </table>
                                          <div class="text-center mt-2">
                                             <a href="/dashboard/fees.php" class="btn btn-primary btn-sm">View All Payments</a>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <?php endif; ?>
