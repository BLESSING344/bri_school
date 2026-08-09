<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SchoolClassModel;
use App\Models\TeacherModel;
use PDOException;

class TeacherController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['admin', 'teacher']);

        $teachers = new TeacherModel();

        if ($this->isPost()) {
            $this->handlePost($teachers);
        }

        try {
            $teacherList = $teachers->allOrdered();
        } catch (PDOException $e) {
            $teacherList = [];
            error_log('Teachers query error: ' . $e->getMessage());
        }

        try {
            $unlinkedTeachers = $teachers->usersWithoutTeacherRecord();
        } catch (PDOException $e) {
            $unlinkedTeachers = [];
        }

        try {
            $classesList = (new SchoolClassModel())->distinctClassNames();
        } catch (PDOException $e) {
            $classesList = [];
        }

        $editTeacher = null;
        if (isset($_GET['edit'])) {
            try {
                $editTeacher = $teachers->find($_GET['edit']);
            } catch (PDOException $e) {
                $editTeacher = null;
            }
        }

        $this->render('teachers/index', [
            'page_title' => 'Teachers Management',
            'teachers' => $teacherList,
            'unlinked_teachers' => $unlinkedTeachers,
            'classes_list' => $classesList,
            'edit_teacher' => $editTeacher,
        ]);
    }

    private function handlePost(TeacherModel $teachers): void
    {
        $action = $this->input('action');

        if ($action === 'add') {
            $data = [
                'full_name' => trim($this->input('full_name', '')),
                'subject' => trim($this->input('subject', '')),
                'phone' => trim($this->input('phone', '')),
                'email' => trim($this->input('email', '')),
                'class_assigned' => trim($this->input('class_assigned', '')),
            ];
            $teachers->insert($data);
            $this->redirect('/dashboard/teachers?success=' . rawurlencode('Teacher added successfully'));
        } elseif ($action === 'edit') {
            $id = $this->input('id');
            $data = [
                'full_name' => trim($this->input('full_name', '')),
                'subject' => trim($this->input('subject', '')),
                'phone' => trim($this->input('phone', '')),
                'email' => trim($this->input('email', '')),
                'class_assigned' => trim($this->input('class_assigned', '')),
            ];
            $teachers->update($id, $data);
            $this->redirect('/dashboard/teachers?success=' . rawurlencode('Teacher updated successfully'));
        } elseif ($action === 'delete') {
            $id = $this->input('id');
            $teachers->delete($id);
            $this->redirect('/dashboard/teachers?success=' . rawurlencode('Teacher deleted successfully'));
        } elseif ($action === 'import_from_user') {
            $fullName = trim($this->input('full_name', ''));

            if ($teachers->existsByName($fullName)) {
                $this->redirect('/dashboard/teachers?error=' . rawurlencode('Teacher already exists'));
            }

            $teachers->insert(['full_name' => $fullName]);
            $this->redirect('/dashboard/teachers?success=' . rawurlencode('Teacher imported from user successfully'));
        }
    }
}
