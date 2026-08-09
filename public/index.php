<?php

require_once __DIR__ . '/../src/bootstrap.php';

use App\Core\Auth;
use App\Controllers\AttendanceController;
use App\Controllers\AuthController;
use App\Controllers\ClassController;
use App\Controllers\DashboardController;
use App\Controllers\ExamController;
use App\Controllers\FeesController;
use App\Controllers\ProfileController;
use App\Controllers\SettingsController;
use App\Controllers\StudentController;
use App\Controllers\TeacherController;
use App\Controllers\UserController;

$routes = [
    '/login' => [AuthController::class, 'login'],
    '/logout' => [AuthController::class, 'logout'],
    '/dashboard' => [DashboardController::class, 'index'],
    '/dashboard/students' => [StudentController::class, 'index'],
    '/dashboard/classes' => [ClassController::class, 'index'],
    '/dashboard/teachers' => [TeacherController::class, 'index'],
    '/dashboard/attendance' => [AttendanceController::class, 'index'],
    '/dashboard/attendance/ajax' => [AttendanceController::class, 'ajax'],
    '/dashboard/exams' => [ExamController::class, 'index'],
    '/dashboard/fees' => [FeesController::class, 'index'],
    '/dashboard/fees/receipt' => [FeesController::class, 'receipt'],
    '/dashboard/users' => [UserController::class, 'index'],
    '/dashboard/profile' => [ProfileController::class, 'index'],
    '/dashboard/settings' => [SettingsController::class, 'index'],
];

$path = rtrim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($path === '') {
    $path = '/';
}

if ($path === '/') {
    header('Location: ' . (Auth::check() ? '/dashboard' : '/login'));
    exit();
}

if (isset($routes[$path])) {
    [$controllerClass, $method] = $routes[$path];
    (new $controllerClass())->$method();
    exit();
}

http_response_code(404);
echo '404 Not Found';
