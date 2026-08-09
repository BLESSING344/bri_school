<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>User Management</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Users List</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#userModal">
                    <i class="fa fa-plus"></i> Add New User
                </button>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td>
                                    <span class="badge badge-info"><?php echo ucfirst($user['role']); ?></span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="/dashboard/users.php?edit=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#userModal" onclick="loadEditData(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#passwordModal" onclick="loadPasswordData(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                        <i class="fa fa-key"></i> Password
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" action="/dashboard/users.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <?php endif; ?>
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

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/dashboard/users.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_user ? 'edit' : 'add'; ?>">
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>" required>
                    </div>

                    <?php if (!$edit_user): ?>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($edit_user['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Role *</label>
                        <select class="form-control" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="teacher" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                            <option value="bursar" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'bursar') ? 'selected' : ''; ?>>Bursar</option>
                        </select>
                    </div>

                    <?php if ($edit_user): ?>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?php echo (isset($edit_user['is_active']) && $edit_user['is_active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_user ? 'Update' : 'Add'; ?> User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/dashboard/users.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="id" id="password_user_id">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="password_username" readonly>
                    </div>
                    <div class="form-group">
                        <label>New Password *</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadEditData(user) {
    document.querySelector('input[name="username"]').value = user.username || '';
    document.querySelector('input[name="full_name"]').value = user.full_name || '';
    document.querySelector('select[name="role"]').value = user.role || '';
    document.querySelector('#is_active').checked = user.is_active == 1;
    document.querySelector('input[name="action"]').value = 'edit';
    document.querySelector('input[name="id"]').value = user.id;
    document.querySelector('.modal-title').textContent = 'Edit User';
}

function loadPasswordData(userId, username) {
    document.getElementById('password_user_id').value = userId;
    document.getElementById('password_username').value = username;
}
</script>
