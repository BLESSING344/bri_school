<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class AttendanceModel extends Model
{
    protected string $table = 'attendance';

    /**
     * Attendance records for a given date (optionally filtered by class),
     * joined with the student's name and class.
     */
    public function recordsForDate(string $date, string $class = ''): array
    {
        $sql = 'SELECT a.*, s.full_name as student_name, s.class
                FROM attendance a
                JOIN students s ON a.student_id = s.id
                WHERE a.date = ?';
        $params = [$date];

        if ($class !== '') {
            $sql .= ' AND s.class = ?';
            $params[] = $class;
        }

        $sql .= ' ORDER BY s.class, s.full_name';

        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Find an existing attendance record for a student on a given date.
     */
    public function findByStudentAndDate($studentId, string $date): ?array
    {
        $row = $this->query(
            'SELECT id FROM attendance WHERE student_id = ? AND date = ?',
            [$studentId, $date]
        )->fetch();

        return $row ?: null;
    }

    /**
     * Mark (insert or update) attendance for a single student on a date.
     */
    public function markAttendance($studentId, string $date, string $status, string $recordedBy): void
    {
        $existing = $this->findByStudentAndDate($studentId, $date);

        if ($existing) {
            $this->query(
                'UPDATE attendance SET status=?, recorded_by=? WHERE id=?',
                [$status, $recordedBy, $existing['id']]
            );
        } else {
            $this->query(
                'INSERT INTO attendance (student_id, date, status, recorded_by) VALUES (?, ?, ?, ?)',
                [$studentId, $date, $status, $recordedBy]
            );
        }
    }

    /**
     * All student ids belonging to a class (used for bulk marking).
     */
    public function studentIdsInClass(string $class): array
    {
        return $this->query('SELECT id FROM students WHERE class = ?', [$class])
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Students (id + full_name) in a class, ordered by name (used by the AJAX endpoint).
     */
    public function studentsInClassOrdered(string $class): array
    {
        return $this->query(
            'SELECT id, full_name FROM students WHERE class = ? ORDER BY full_name',
            [$class]
        )->fetchAll();
    }
}
