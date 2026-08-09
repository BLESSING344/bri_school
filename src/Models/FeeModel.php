<?php

namespace App\Models;

use App\Core\Model;

class FeeModel extends Model
{
    protected string $table = 'fees';

    /**
     * Fee records joined with student info, optionally filtered by student and/or term.
     */
    public function allWithStudentFiltered(?string $studentId, ?string $term): array
    {
        $sql = "SELECT f.*, s.full_name AS student_name, s.class
                FROM fees f
                JOIN students s ON f.student_id = s.id
                WHERE 1=1";
        $params = [];

        if ($studentId) {
            $sql .= ' AND f.student_id = ?';
            $params[] = $studentId;
        }

        if ($term) {
            $sql .= ' AND f.term = ?';
            $params[] = $term;
        }

        $sql .= ' ORDER BY f.payment_date DESC';

        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * A single fee record joined with student info, for receipt display.
     */
    public function findWithStudent(int $id): ?array
    {
        $sql = "SELECT f.*, s.full_name AS student_name, s.class, s.parent_name, s.parent_contact
                FROM fees f
                JOIN students s ON f.student_id = s.id
                WHERE f.id = ?";

        $row = $this->query($sql, [$id])->fetch();

        return $row ?: null;
    }

    /**
     * Totals (amount paid, balance, record count), optionally filtered by student and/or term.
     */
    public function summary(?string $studentId, ?string $term): array
    {
        $sql = 'SELECT
                    SUM(amount_paid) AS total_paid,
                    SUM(balance) AS total_balance,
                    COUNT(*) AS total_records
                FROM fees WHERE 1=1';
        $params = [];

        if ($studentId) {
            $sql .= ' AND student_id = ?';
            $params[] = $studentId;
        }

        if ($term) {
            $sql .= ' AND term = ?';
            $params[] = $term;
        }

        $stmt = $this->query($sql, $params);
        return $stmt->fetch() ?: ['total_paid' => 0, 'total_balance' => 0, 'total_records' => 0];
    }
}
