<?php

namespace App\Models;

use App\Core\Model;

class TeacherModel extends Model
{
    protected string $table = 'teachers';

    public function allOrdered(): array
    {
        return $this->query('SELECT * FROM teachers ORDER BY full_name')->fetchAll();
    }

    public function distinctFullNames(): array
    {
        return $this->query('SELECT DISTINCT full_name FROM teachers ORDER BY full_name')
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function usersWithoutTeacherRecord(): array
    {
        return $this->query(
            "SELECT u.id, u.username, u.full_name
             FROM users u
             LEFT JOIN teachers t ON u.full_name = t.full_name
             WHERE u.role = 'teacher' AND t.id IS NULL
             ORDER BY u.full_name"
        )->fetchAll();
    }

    public function existsByName(string $fullName): bool
    {
        $stmt = $this->query('SELECT id FROM teachers WHERE full_name = ?', [$fullName]);
        return (bool) $stmt->fetch();
    }
}
