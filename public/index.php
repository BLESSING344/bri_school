<?php

require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Auth;

if (Auth::check()) {
    header('Location: /dashboard/index.php');
} else {
    header('Location: /login.php');
}
exit();
