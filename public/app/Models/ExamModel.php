<?php

namespace App\Models;

use App\Core\Model;

class ExamModel extends Model
{
    protected string $table = 'exams';

    public function allOrdered(): array
    {
        return $this->query('SELECT * FROM exams ORDER BY year DESC, term, exam_name')->fetchAll();
    }
}
