<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ExamModel;
use App\Models\MarkModel;
use App\Models\SchoolClassModel;
use PDOException;

class ExamController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['admin', 'teacher']);

        $exams = new ExamModel();
        $marks = new MarkModel();

        if ($this->isPost()) {
            $this->handlePost($exams, $marks);
        }

        try {
            $examList = $exams->allOrdered();
        } catch (PDOException $e) {
            $examList = [];
            error_log('Exams query error: ' . $e->getMessage());
        }

        try {
            $classesList = (new SchoolClassModel())->distinctClassNames();
        } catch (PDOException $e) {
            $classesList = [];
        }

        $selectedExam = null;
        $examMarks = [];
        $examStudents = [];

        if (isset($_GET['exam_id'])) {
            try {
                $selectedExam = $exams->find($_GET['exam_id']);
            } catch (PDOException $e) {
                $selectedExam = null;
            }

            if ($selectedExam) {
                try {
                    $examMarks = $marks->forExamWithStudents($selectedExam['id']);
                } catch (PDOException $e) {
                    $examMarks = [];
                    error_log('Exam marks query error: ' . $e->getMessage());
                }

                try {
                    $examStudents = $marks->studentsForClass($selectedExam['class'] ?? null);
                } catch (PDOException $e) {
                    $examStudents = [];
                }
            }
        }

        $this->render('exams/index', [
            'page_title' => 'Exams & Marks Management',
            'exams' => $examList,
            'classes_list' => $classesList,
            'selected_exam' => $selectedExam,
            'exam_marks' => $examMarks,
            'exam_students' => $examStudents,
        ]);
    }

    private function handlePost(ExamModel $exams, MarkModel $marks): void
    {
        $action = $this->input('action');

        if ($action === 'add_exam') {
            $data = [
                'exam_name' => trim($this->input('exam_name', '')),
                'term' => trim($this->input('term', '')),
                'year' => $this->input('year', date('Y')),
                'class' => trim($this->input('class', '')),
            ];

            try {
                $exams->insert($data);
                $this->redirect('/dashboard/exams?success=' . rawurlencode('Exam created successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/exams?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'add_marks') {
            $examId = $this->input('exam_id');
            $studentId = $this->input('student_id');
            $subject = trim($this->input('subject', ''));
            $score = $this->input('score');

            try {
                $existing = $marks->findExisting($studentId, $examId, $subject);

                if ($existing) {
                    $marks->update($existing['id'], ['score' => $score]);
                } else {
                    $marks->insert([
                        'student_id' => $studentId,
                        'exam_id' => $examId,
                        'subject' => $subject,
                        'score' => $score,
                    ]);
                }

                $this->redirect('/dashboard/exams?success=' . rawurlencode('Marks added successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/exams?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'delete_exam') {
            $id = $this->input('id');

            try {
                $exams->delete($id);
                $this->redirect('/dashboard/exams?success=' . rawurlencode('Exam deleted successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/exams?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        }
    }
}
