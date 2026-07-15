<?php

namespace App\Models;

use App\Core\Model;

class SchoolClassModel extends Model
{
    protected string $table = 'classes';

    public function distinctClassNames(): array
    {
        return $this->query('SELECT DISTINCT class_name FROM classes ORDER BY class_name')
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function allWithStudentCount(): array
    {
        return $this->query(
            'SELECT c.*, COUNT(s.id) as student_count
             FROM classes c
             LEFT JOIN students s ON s.class = c.class_name
             GROUP BY c.id
             ORDER BY c.class_name'
        )->fetchAll();
    }
}
