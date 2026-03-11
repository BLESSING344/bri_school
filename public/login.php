<?php
ob_start(); 
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    ob_end_clean(); 
    header('Location: dashboard/index.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } else {
        try {
            // Remove email from SELECT and WHERE clause since it doesn't exist
            $sql = "SELECT id, username, password, full_name, role, is_active FROM users WHERE username = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username]);  // Remove second parameter since we're only checking username
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Check if user is active
                if (!$user['is_active']) {
                    $error_message = 'Your account has been deactivated. Please contact administrator.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Update last login using created_at since there's no last_login column
                    try {
                        $sql = "UPDATE users SET created_at = NOW() WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$user['id']]);
                    } catch(PDOException $e) {
                        // Ignore update errors and continue with login
                    }
                    
                    ob_end_clean(); 
                    header('Location: dashboard/index.php');
                    exit();
                }
            } else {
                $error_message = 'Invalid username or password.';
            }
        } catch(PDOException $e) {
            // For debugging - remove in production
            error_log($e->getMessage());
            $error_message = 'Database error. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>BRI School Management System - Login</title>
      <meta name="keywords" content="school management, education, student management">
      <meta name="description" content="BRI International School Management System">
      <meta name="author" content="BRI School">
      <!-- site icon -->
      <link rel="icon" href="images/fevicon.png" type="image/png" />
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css" />
      <!-- site css -->
      <link rel="stylesheet" href="style.css" />
      <!-- responsive css -->
      <link rel="stylesheet" href="css/responsive.css" />
      <!-- color css -->
      <link rel="stylesheet" href="css/colors.css" />
      <!-- select bootstrap -->
      <link rel="stylesheet" href="css/bootstrap-select.css" />
      <!-- scrollbar css -->
      <link rel="stylesheet" href="css/perfect-scrollbar.css" />
      <!-- custom css -->
      <link rel="stylesheet" href="css/custom.css" />
      <!-- calendar file css -->
      <link rel="stylesheet" href="js/semantic.min.css" />
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <body class="inner_page login">
      <div class="full_container">
         <div class="container">
            <div class="center verticle_center full_height">
               <div class="login_section">
                  <div class="logo_login">
                     <div class="center">
                        <img width="210" src="images/logo/logo.png" alt="BRI School Logo" />
                        <h3 class="mt-3" style="color: #333;">BRI International School</h3>
                        <p style="color: #666; font-size: 14px;">Management System</p>
                     </div>
                  </div>
                  <div class="login_form">
                     <?php if (isset($error_message) && !empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>


                     <form method="POST" action="">
                        <fieldset>
                           <div class="field">
                              <label class="label_field">Username</label>
                              <input type="text" name="username" placeholder="Username" />
                           </div>
                           <div class="field">
                              <label class="label_field">Password</label>
                              <input type="password" name="password" placeholder="Password" />
                           </div>
                           <div class="field margin_0">
                              <label class="label_field hidden">hidden label</label>
                              <button type="submit" class="main_bt">Sign In</button>
                           </div>
                        </fieldset>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- jQuery -->
      <script src="js/jquery.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.min.js"></script>
      <!-- wow animation -->
      <script src="js/animate.js"></script>
      <!-- select country -->
      <script src="js/bootstrap-select.js"></script>
      <!-- nice scrollbar -->
      <script src="js/perfect-scrollbar.min.js"></script>
      <script>
         var ps = new PerfectScrollbar('#sidebar');
      </script>
      <!-- custom js -->
      <script src="js/custom.js"></script>
   </body>
</html>