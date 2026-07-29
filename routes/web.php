<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return redirect('/dashboard/index.php');
})->middleware('auth');
use App\Http\Controllers\ReportCardController;

// 1. Route to generate and download the PDF report card
Route::get('/report-card/download/{id}', [ReportCardController::class, 'downloadReportCard'])->name('reportcard.download');

// 2. Official Student Presentation Page for your Lecturer
Route::get('/test-student', function () {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>BRI School Management System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light p-5">
        <div class="container">
            <div class="card shadow-lg mx-auto border-0" style="max-width: 550px; border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">BRI INTERNATIONAL SCHOOL</h4>
                    <small>Student Academic Management Portal</small>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <span class="badge bg-secondary mb-2">Student ID: #1</span>
                        <h3 class="fw-bold text-dark">John Mugisha</h3>
                        <p class="text-muted mb-1"><strong>Class:</strong> Primary One</p>
                        <p class="text-muted"><strong>Parent Contact:</strong> 0700123456</p>
                    </div>
                    <hr>
                    <p class="small text-secondary mb-3">Click the button below to generate and stream the official PDF report card:</p>
                    
                    <a href="' . route('reportcard.download', 1) . '" 
                       class="btn btn-success btn-lg w-100 py-2 fw-semibold shadow-sm" 
                       target="_blank">
                       📄 Print / Download Report Card PDF
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>';
});