<?php

require_once __DIR__ . '/../src/bootstrap.php';

(new App\Controllers\AuthController())->logout();
