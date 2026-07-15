<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SchoolClassModel;
use App\Models\StudentModel;
use PDOException;

class StudentController extends Controller
{
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
                $this->redirect('/dashboard/students.php?success=' . rawurlencode('Student added successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/students.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
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
                $this->redirect('/dashboard/students.php?success=' . rawurlencode('Student updated successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/students.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'delete') {
            $id = $this->input('id');

            try {
                $students->delete($id);
                $this->redirect('/dashboard/students.php?success=' . rawurlencode('Student deleted successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/students.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        }
    }
}
