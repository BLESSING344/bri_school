<?php

namespace App\Models;

use App\Core\Model;

class StudentModel extends Model
{
    protected string $table = 'students';

    public function allOrdered(): array
    {
        return $this->query('SELECT * FROM students ORDER BY class, full_name')->fetchAll();
    }
}
