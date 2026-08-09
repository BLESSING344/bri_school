<?php

require_once __DIR__ . '/../../src/bootstrap.php';

(new App\Controllers\AttendanceController())->index();
