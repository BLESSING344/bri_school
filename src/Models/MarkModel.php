<?php

namespace App\Models;

use App\Core\Model;

class MarkModel extends Model
{
    protected string $table = 'marks';

    /**
     * Marks recorded for a given exam, joined with the student's name and class.
     */
    public function forExamWithStudents($examId): array
    {
        return $this->query(
            'SELECT m.*, s.full_name as student_name, s.class
             FROM marks m
             JOIN students s ON m.student_id = s.id
             WHERE m.exam_id = ?
             ORDER BY s.class, s.full_name, m.subject',
            [$examId]
        )->fetchAll();
    }

    /**
     * Find an existing mark for a given student/exam/subject combination, if any.
     */
    public function findExisting($studentId, $examId, string $subject): ?array
    {
        $stmt = $this->query(
            'SELECT id FROM marks WHERE student_id = ? AND exam_id = ? AND subject = ?',
            [$studentId, $examId, $subject]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Students eligible to receive marks for an exam, optionally restricted to one class.
     */
    public function studentsForClass(?string $className): array
    {
        if ($className) {
            return $this->query(
                'SELECT id, full_name FROM students WHERE class = ? ORDER BY full_name',
                [$className]
            )->fetchAll();
        }

        return $this->query('SELECT id, full_name FROM students ORDER BY full_name')->fetchAll();
    }
}
