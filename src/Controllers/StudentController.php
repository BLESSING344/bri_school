<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\MarkModel;
use App\Models\SchoolClassModel;
use App\Models\StudentModel;
use PDOException;

class StudentController extends Controller
{
    public function reportCard(): void
    {
        $this->requireRole(['admin', 'teacher']);

        $id = (int) $this->input('id', 0);
        $student = $id ? (new StudentModel())->find($id) : null;

        if (!$student) {
            $this->redirect('/dashboard/students?error=' . rawurlencode('Student not found'));
        }

        $marks = (new MarkModel())->forStudent($id);

        foreach ($marks as &$mark) {
            [$mark['grade'], $mark['remark']] = self::gradeFor((float) $mark['score']);
        }
        unset($mark);

        $this->renderBare('students/report_card', [
            'page_title' => 'Report Card',
            'student' => $student,
            'marks' => $marks,
        ]);
    }

    /**
     * @return array{0: string, 1: string} [grade, remark] for a percentage score.
     */
    private static function gradeFor(float $score): array
    {
        return match (true) {
            $score >= 80 => ['D1', 'Excellent'],
            $score >= 70 => ['D2', 'Very Good'],
            $score >= 60 => ['C3', 'Good'],
            $score >= 50 => ['C4', 'Fair'],
            $score >= 40 => ['C5', 'Fair'],
            $score >= 30 => ['C6', 'Pass'],
            $score >= 20 => ['P7', 'Pass'],
            $score >= 10 => ['P8', 'Weak'],
            default => ['F9', 'Weak'],
        };
    }
    public function index(): void
    {
        $this->requireRole(['admin', 'teacher']);

        $students = new StudentModel();
        $classes = new SchoolClassModel();

        if ($this->isPost()) {
            $this->handlePost($students);
        }

        try {
            $studentList = $students->allOrdered();
        } catch (PDOException $e) {
            $studentList = [];
            error_log('Students query error: ' . $e->getMessage());
        }

        try {
            $classesList = $classes->distinctClassNames();
        } catch (PDOException $e) {
            $classesList = [];
        }

        $editStudent = null;
        if (isset($_GET['edit'])) {
            try {
                $editStudent = $students->find($_GET['edit']);
            } catch (PDOException $e) {
                $editStudent = null;
            }
        }

        $this->render('students/index', [
            'page_title' => 'Students Management',
            'students' => $studentList,
            'classes_list' => $classesList,
            'edit_student' => $editStudent,
        ]);
    }

    private function handlePost(StudentModel $students): void
    {
        $action = $this->input('action');

        if ($action === 'add') {
            $data = [
                'full_name' => trim($this->input('full_name', '')),
                'gender' => $this->input('gender'),
                'class' => trim($this->input('class', '')),
                'parent_name' => trim($this->input('parent_name', '')),
                'parent_contact' => trim($this->input('parent_contact', '')),
            ];

            try {
                $students->insert($data);
                $this->redirect('/dashboard/students?success=' . rawurlencode('Student added successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/students?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'edit') {
            $id = $this->input('id');
            $data = [
                'full_name' => trim($this->input('full_name', '')),
                'gender' => $this->input('gender'),
                'class' => trim($this->input('class', '')),
                'parent_name' => trim($this->input('parent_name', '')),
                'parent_contact' => trim($this->input('parent_contact', '')),
            ];

            try {
                $students->update($id, $data);
                $this->redirect('/dashboard/students?success=' . rawurlencode('Student updated successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/students?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'delete') {
            $id = $this->input('id');

            try {
                $students->delete($id);
                $this->redirect('/dashboard/students?success=' . rawurlencode('Student deleted successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/students?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        }
    }
}
