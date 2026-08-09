<?php

require_once __DIR__ . '/../../src/bootstrap.php';

(new App\Controllers\StudentController())->index();
