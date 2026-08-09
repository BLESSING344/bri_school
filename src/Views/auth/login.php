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
      <link rel="icon" href="/images/fevicon.png" type="image/png" />
      <!-- bootstrap css -->
      <link rel="stylesheet" href="/css/bootstrap.min.css" />
      <!-- site css -->
      <link rel="stylesheet" href="/style.css" />
      <!-- responsive css -->
      <link rel="stylesheet" href="/css/responsive.css" />
      <!-- color css -->
      <link rel="stylesheet" href="/css/colors.css" />
      <!-- select bootstrap -->
      <link rel="stylesheet" href="/css/bootstrap-select.css" />
      <!-- scrollbar css -->
      <link rel="stylesheet" href="/css/perfect-scrollbar.css" />
      <!-- custom css -->
      <link rel="stylesheet" href="/css/custom.css" />
      <!-- calendar file css -->
      <link rel="stylesheet" href="/js/semantic.min.css" />
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
                        <h2 style="color: #fff; margin: 0; font-weight: 600;">BRI International School</h2>
                        <p style="color: #dfe6ec; font-size: 14px; margin-top: 6px;">Management System</p>
                     </div>
                  </div>
                  <div class="login_form">
                     <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>


                     <form method="POST" action="">
                        <fieldset>
                           <div class="field">
                              <label class="label_field">Username</label>
                              <input type="text" name="username" placeholder="Username" autocomplete="username" autofocus />
                           </div>
                           <div class="field">
                              <label class="label_field">Password</label>
                              <input type="password" name="password" placeholder="Password" autocomplete="current-password" />
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
      <script src="/js/jquery.min.js"></script>
      <script src="/js/popper.min.js"></script>
      <script src="/js/bootstrap.min.js"></script>
      <!-- wow animation -->
      <script src="/js/animate.js"></script>
      <!-- select country -->
      <script src="/js/bootstrap-select.js"></script>
      <!-- nice scrollbar -->
      <script src="/js/perfect-scrollbar.min.js"></script>
      <script>
         var ps = new PerfectScrollbar('#sidebar');
      </script>
      <!-- custom js -->
      <script src="/js/custom.js"></script>
   </body>
</html>
