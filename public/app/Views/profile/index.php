<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>My Profile</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Profile Information -->
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Profile Information</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <form method="POST" action="/dashboard/profile.php">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-group">
                        <label for="full_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" required>
                    </div>

                    <?php if (isset($user_data['email'])): ?>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
                        <small class="form-text text-muted">Email cannot be changed here</small>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" value="<?php echo ucfirst(htmlspecialchars($user_data['role'] ?? 'User')); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Account Status</label>
                        <input type="text" class="form-control"
                               value="<?php echo ($user_data['is_active'] ?? 0) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Change Password -->
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Change Password</h2>
                </div>
            </div>
            <div class="table_section padding_infor_info">
                <form method="POST" action="/dashboard/profile.php" id="passwordForm">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label for="current_password">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                        <small class="form-text text-muted">Minimum 6 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validate password match on form submit
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    var newPassword = document.getElementById('new_password').value;
    var confirmPassword = document.getElementById('confirm_password').value;

    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('New passwords do not match!');
        return false;
    }

    if (newPassword.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long!');
        return false;
    }

    return true;
});
</script>
