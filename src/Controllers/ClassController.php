<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SchoolClassModel;
use App\Models\TeacherModel;
use PDOException;

class ClassController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['admin', 'teacher']);

        $classes = new SchoolClassModel();

        if ($this->isPost()) {
            $this->handlePost($classes);
        }

        try {
            $classList = $classes->allWithStudentCount();
        } catch (PDOException $e) {
            $classList = [];
            error_log('Classes query error: ' . $e->getMessage());
        }

        try {
            $teachersList = (new TeacherModel())->distinctFullNames();
        } catch (PDOException $e) {
            $teachersList = [];
            error_log('Classes page teachers query error: ' . $e->getMessage());
        }

        $editClass = null;
        if (isset($_GET['edit'])) {
            try {
                $editClass = $classes->find($_GET['edit']);
            } catch (PDOException $e) {
                $editClass = null;
            }
        }

        $this->render('classes/index', [
            'page_title' => 'Classes Management',
            'classes' => $classList,
            'teachers_list' => $teachersList,
            'edit_class' => $editClass,
        ]);
    }

    private function handlePost(SchoolClassModel $classes): void
    {
        $action = $this->input('action');

        if ($action === 'add') {
            $data = [
                'class_name' => trim($this->input('class_name', '')),
                'teacher_in_charge' => trim($this->input('teacher_in_charge', '')),
            ];
            $classes->insert($data);
            $this->redirect('/dashboard/classes?success=' . rawurlencode('Class added successfully'));
        } elseif ($action === 'edit') {
            $id = $this->input('id');
            $data = [
                'class_name' => trim($this->input('class_name', '')),
                'teacher_in_charge' => trim($this->input('teacher_in_charge', '')),
            ];
            $classes->update($id, $data);
            $this->redirect('/dashboard/classes?success=' . rawurlencode('Class updated successfully'));
        } elseif ($action === 'delete') {
            $id = $this->input('id');
            $classes->delete($id);
            $this->redirect('/dashboard/classes?success=' . rawurlencode('Class deleted successfully'));
        }
    }
}
