<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <title>BRI School Management - <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></title>
      <link rel="icon" href="/images/fevicon.png" type="image/png" />
      <link rel="stylesheet" href="/css/bootstrap.min.css" />
      <link rel="stylesheet" href="/style.css" />
      <link rel="stylesheet" href="/css/responsive.css" />
      <link rel="stylesheet" href="/css/bootstrap-select.css" />
      <link rel="stylesheet" href="/css/perfect-scrollbar.css" />
      <link rel="stylesheet" href="/css/custom.css" />
      <link rel="stylesheet" href="/css/flaticon.css" />
      <link rel="stylesheet" href="/css/font-awesome.min.css" />
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <body class="dashboard dashboard_1">
      <div class="full_container">
         <div class="inner_container">
            <!-- Sidebar  -->
            <nav id="sidebar">
               <div class="sidebar_blog_1">
                  <div class="sidebar-header">
                     <div class="logo_section">
                        <a href="/dashboard/index.php"><img class="logo_icon img-responsive" src="/images/logo/logo_icon.png" alt="#" /></a>
                     </div>
                  </div>
                  <div class="sidebar_user_info">
                     <div class="icon_setting"></div>
                     <div class="user_profle_side">
                        <div class="user_info">
                           <h6><?php echo htmlspecialchars($current_user['full_name'] ?? 'User'); ?></h6>
                           <p><span class="online_animation"></span> <?php echo ucfirst($current_user['role'] ?? 'User'); ?></p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="sidebar_blog_2">
                  <h4>Main Menu</h4>
                  <ul class="list-unstyled components">
                     <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <a href="/dashboard/index.php"><i class="fa fa-dashboard yellow_color"></i> <span>Dashboard</span></a>
                     </li>

                     <?php if ($is_admin || $is_teacher): ?>
                     <li>
                        <a href="#academics" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-graduation-cap orange_color"></i> <span>Academics</span></a>
                        <ul class="collapse list-unstyled <?php echo (strpos($_SERVER['PHP_SELF'], 'students') !== false || strpos($_SERVER['PHP_SELF'], 'teachers') !== false || strpos($_SERVER['PHP_SELF'], 'classes') !== false || strpos($_SERVER['PHP_SELF'], 'attendance') !== false || strpos($_SERVER['PHP_SELF'], 'exams') !== false) ? 'show' : ''; ?>" id="academics">
                           <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'students') !== false) ? 'active' : ''; ?>">
                              <a href="/dashboard/students.php"><i class="fa fa-users blue1_color"></i> <span>Students</span></a>
                           </li>
                           <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'teachers') !== false) ? 'active' : ''; ?>">
                              <a href="/dashboard/teachers.php"><i class="fa fa-user green_color"></i> <span>Teachers</span></a>
                           </li>
                           <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'classes') !== false) ? 'active' : ''; ?>">
                              <a href="/dashboard/classes.php"><i class="fa fa-building orange_color"></i> <span>Classes</span></a>
                           </li>
                           <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'attendance') !== false) ? 'active' : ''; ?>">
                              <a href="/dashboard/attendance.php"><i class="fa fa-check-square purple_color"></i> <span>Attendance</span></a>
                           </li>
                           <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'exams') !== false) ? 'active' : ''; ?>">
                              <a href="/dashboard/exams.php"><i class="fa fa-file-text red_color"></i> <span>Exams & Marks</span></a>
                           </li>
                        </ul>
                     </li>
                     <?php endif; ?>

                     <?php if ($is_admin || $is_bursar): ?>
                     <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'fees') !== false) ? 'active' : ''; ?>">
                        <a href="/dashboard/fees.php"><i class="fa fa-money yellow_color"></i> <span>Fees & Payments</span></a>
                     </li>
                     <?php endif; ?>

                     <?php if ($is_admin): ?>
                     <li>
                        <a href="#administration" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-cog blue2_color"></i> <span>Administration</span></a>
                        <ul class="collapse list-unstyled <?php echo (strpos($_SERVER['PHP_SELF'], 'users') !== false) ? 'show' : ''; ?>" id="administration">
                           <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'users') !== false) ? 'active' : ''; ?>">
                              <a href="/dashboard/users.php"><i class="fa fa-cog blue2_color"></i> <span>User Management</span></a>
                           </li>
                        </ul>
                     </li>
                     <?php endif; ?>

                     <li class="<?php echo (strpos($_SERVER['PHP_SELF'], 'profile') !== false) ? 'active' : ''; ?>">
                        <a href="/dashboard/profile.php"><i class="fa fa-user-circle blue2_color"></i> <span>My Profile</span></a>
                     </li>

                     <li class="mt-3">
                        <a href="/logout.php"><i class="fa fa-sign-out red_color"></i> <span>Logout</span></a>
                     </li>
                  </ul>
               </div>
            </nav>
            <!-- end sidebar -->
            <!-- right content -->
            <div id="content">
               <!-- topbar -->
               <div class="topbar">
                  <nav class="navbar navbar-expand-lg navbar-light">
                     <div class="full">
                        <button type="button" id="sidebarCollapse" class="sidebar_toggle"><i class="fa fa-bars"></i></button>
                        <div class="logo_section">
                           <a href="/dashboard/index.php"><img class="img-responsive" src="/images/logo/logo.png" alt="#" /></a>
                        </div>
                        <div class="right_topbar">
                           <div class="icon_info">
                              <ul class="user_profile_dd">
                                 <li>
                                    <a class="dropdown-toggle" data-toggle="dropdown">
                                       <span class="name_user"><?php echo htmlspecialchars($current_user['full_name'] ?? 'User'); ?></span>
                                       <span class="badge badge-primary ml-2"><?php echo ucfirst($current_user['role'] ?? 'User'); ?></span>
                                    </a>
                   <div class="dropdown-menu">
                      <a class="dropdown-item" href="/dashboard/index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                      <a class="dropdown-item" href="/dashboard/profile.php"><i class="fa fa-user-circle"></i> My Profile</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="/logout.php"><span>Log Out</span> <i class="fa fa-sign-out"></i></a>
                   </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </nav>
               </div>
               <!-- end topbar -->
               <!-- dashboard inner -->
               <div class="midde_cont">
                  <div class="container-fluid">
                     <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                           <?php echo htmlspecialchars($_GET['success']); ?>
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                     <?php endif; ?>
                     <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                           <?php echo htmlspecialchars($_GET['error']); ?>
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                     <?php endif; ?>
