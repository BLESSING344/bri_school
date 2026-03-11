<?php
// Require auth and database BEFORE any output (auth.php handles session_start)
require_once __DIR__ . '/includes/auth.php';

// Check authentication BEFORE any output
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get current user data
$current_user_id = $_SESSION['user_id'];
$user_data = getCurrentUser($pdo);

// Handle form submissions BEFORE including header (to allow redirects)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update_profile') {
            $full_name = trim($_POST['full_name']);
            $username = trim($_POST['username']);
            
            // Validate required fields
            if (empty($full_name) || empty($username)) {
                header('Location: profile.php?error=Full name and username are required');
                exit();
            }
            
            try {
                // Check if username is already taken by another user
                $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$username, $current_user_id]);
                
                if ($check_stmt->fetch()) {
                    header('Location: profile.php?error=Username already taken by another user');
                    exit();
                }
                
                // Update user profile
                $sql = "UPDATE users SET full_name = ?, username = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$full_name, $username, $current_user_id])) {
                    // Update session
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['username'] = $username;
                    
                    header('Location: profile.php?success=Profile updated successfully');
                    exit();
                } else {
                    header('Location: profile.php?error=Failed to update profile');
                    exit();
                }
            } catch (PDOException $e) {
                header('Location: profile.php?error=Error: ' . urlencode($e->getMessage()));
                exit();
            }
        }
        elseif ($_POST['action'] == 'change_password') {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Validate passwords
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                header('Location: profile.php?error=All password fields are required');
                exit();
            }
            
            if ($new_password !== $confirm_password) {
                header('Location: profile.php?error=New passwords do not match');
                exit();
            }
            
            if (strlen($new_password) < 6) {
                header('Location: profile.php?error=New password must be at least 6 characters long');
                exit();
            }
            
            try {
                // Verify current password
                $sql = "SELECT password FROM users WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$current_user_id]);
                $user = $stmt->fetch();
                
                if (!$user || !password_verify($current_password, $user['password'])) {
                    header('Location: profile.php?error=Current password is incorrect');
                    exit();
                }
                
                // Update password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $pdo->prepare($update_sql);
                
                if ($update_stmt->execute([$new_password_hash, $current_user_id])) {
                    header('Location: profile.php?success=Password changed successfully');
                    exit();
                } else {
                    header('Location: profile.php?error=Failed to change password');
                    exit();
                }
            } catch (PDOException $e) {
                header('Location: profile.php?error=Error: ' . urlencode($e->getMessage()));
                exit();
            }
        }
    }
}

// Refresh user data after potential updates
$user_data = getCurrentUser($pdo);

// Now include header after POST handling is done
require_once __DIR__ . '/includes/header.php';

$page_title = 'My Profile';
?>

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
                <form method="POST" action="profile.php">
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
                <form method="POST" action="profile.php" id="passwordForm">
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>

