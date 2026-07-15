<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDOException;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $pdo = Database::connection();
        $stats = [];

        try {
            $stats['students'] = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
        } catch (PDOException $e) {
            $stats['students'] = 0;
        }

        try {
            $stats['teachers'] = (int) $pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();
        } catch (PDOException $e) {
            $stats['teachers'] = 0;
        }

        try {
            $stats['classes'] = (int) $pdo->query('SELECT COUNT(*) FROM classes')->fetchColumn();
        } catch (PDOException $e) {
            $stats['classes'] = 0;
        }

        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM attendance WHERE date = CURRENT_DATE');
            $stmt->execute();
            $stats['attendance_today'] = (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $stats['attendance_today'] = 0;
        }

        try {
            $stats['exams'] = (int) $pdo->query('SELECT COUNT(*) FROM exams')->fetchColumn();
        } catch (PDOException $e) {
            $stats['exams'] = 0;
        }

        try {
            $stats['fees_collected'] = (float) $pdo->query('SELECT COALESCE(SUM(amount_paid), 0) FROM fees')->fetchColumn();
        } catch (PDOException $e) {
            $stats['fees_collected'] = 0;
        }

        try {
            $recent_students = $pdo->query('SELECT * FROM students ORDER BY created_at DESC LIMIT 5')->fetchAll();
        } catch (PDOException $e) {
            $recent_students = [];
        }

        try {
            $recent_attendance = $pdo->query(
                'SELECT a.*, s.full_name as student_name, s.class
                 FROM attendance a
                 JOIN students s ON a.student_id = s.id
                 ORDER BY a.date DESC, a.id DESC
                 LIMIT 5'
            )->fetchAll();
        } catch (PDOException $e) {
            $recent_attendance = [];
        }

        try {
            $recent_payments = $pdo->query(
                'SELECT f.*, s.full_name as student_name
                 FROM fees f
                 JOIN students s ON f.student_id = s.id
                 ORDER BY f.payment_date DESC
                 LIMIT 5'
            )->fetchAll();
        } catch (PDOException $e) {
            $recent_payments = [];
        }

        $this->render('dashboard/index', [
            'page_title' => 'Dashboard',
            'stats' => $stats,
            'recent_students' => $recent_students,
            'recent_attendance' => $recent_attendance,
            'recent_payments' => $recent_payments,
        ]);
    }
}
