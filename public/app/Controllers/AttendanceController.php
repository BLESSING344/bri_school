<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\AttendanceModel;
use App\Models\SchoolClassModel;
use PDOException;

class AttendanceController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['admin', 'teacher']);

        $attendance = new AttendanceModel();

        if ($this->isPost()) {
            $this->handlePost($attendance);
        }

        $filterDate = $this->input('date', date('Y-m-d'));
        $filterClass = $this->input('class', '');

        try {
            $classesList = (new SchoolClassModel())->distinctClassNames();
        } catch (PDOException $e) {
            $classesList = [];
            error_log('Attendance page classes query error: ' . $e->getMessage());
        }

        $attendanceRecords = [];
        if ($filterDate) {
            try {
                $attendanceRecords = $attendance->recordsForDate($filterDate, $filterClass);
            } catch (PDOException $e) {
                $attendanceRecords = [];
                error_log('Attendance records query error: ' . $e->getMessage());
            }
        }

        $this->render('attendance/index', [
            'page_title' => 'Attendance Management',
            'filter_date' => $filterDate,
            'filter_class' => $filterClass,
            'classes_list' => $classesList,
            'attendance_records' => $attendanceRecords,
        ]);
    }

    private function handlePost(AttendanceModel $attendance): void
    {
        $action = $this->input('action');
        $recordedBy = $_SESSION['full_name'] ?? '';

        if ($action === 'mark') {
            $studentId = $this->input('student_id');
            $date = $this->input('date');
            $status = $this->input('status');

            try {
                $attendance->markAttendance($studentId, $date, $status, $recordedBy);
                $this->redirect('/dashboard/attendance.php?success=' . rawurlencode('Attendance marked successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/attendance.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'bulk_mark') {
            $date = $this->input('bulk_date');
            $class = $this->input('bulk_class');

            try {
                $studentIds = $attendance->studentIdsInClass($class);

                $count = 0;
                foreach ($studentIds as $studentId) {
                    $status = $this->input('attendance_' . $studentId, 'Absent');
                    $attendance->markAttendance($studentId, $date, $status, $recordedBy);
                    $count++;
                }

                $this->redirect('/dashboard/attendance.php?success=' . rawurlencode('Bulk attendance marked successfully for ' . $count . ' students'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/attendance.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        }
    }

    /**
     * JSON AJAX endpoint used by the attendance page's "Load Students" feature.
     */
    public function ajax(): void
    {
        if (!Auth::check()) {
            $this->json(['success' => false, 'message' => 'Not authenticated']);
        }

        if (!Auth::isAdmin() && !Auth::isTeacher()) {
            $this->json(['success' => false, 'message' => 'Access denied']);
        }

        $action = $this->input('action');

        if ($action === 'get_students') {
            $class = $this->input('class', '');

            if (empty($class)) {
                $this->json(['success' => false, 'message' => 'Class not specified']);
            }

            try {
                $students = (new AttendanceModel())->studentsInClassOrdered($class);
                $this->json(['success' => true, 'students' => $students]);
            } catch (PDOException $e) {
                $this->json(['success' => false, 'message' => 'Database error occurred']);
            }
        } else {
            $this->json(['success' => false, 'message' => 'Invalid action']);
        }
    }
}
