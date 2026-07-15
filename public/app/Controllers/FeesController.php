<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\FeeModel;
use App\Models\StudentModel;
use PDOException;

class FeesController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['admin', 'bursar']);

        $fees = new FeeModel();
        $students = new StudentModel();

        if ($this->isPost()) {
            $this->handlePost($fees);
        }

        $filterStudent = $this->input('student', '');
        $filterTerm = $this->input('term', '');

        try {
            $studentsList = $students->all('full_name');
        } catch (PDOException $e) {
            $studentsList = [];
            error_log('Fees page students query error: ' . $e->getMessage());
        }

        try {
            $feesRecords = $fees->allWithStudentFiltered($filterStudent ?: null, $filterTerm ?: null);
        } catch (PDOException $e) {
            $feesRecords = [];
            error_log('Fees query error: ' . $e->getMessage());
        }

        try {
            $summary = $fees->summary($filterStudent ?: null, $filterTerm ?: null);
        } catch (PDOException $e) {
            $summary = ['total_paid' => 0, 'total_balance' => 0, 'total_records' => 0];
            error_log('Fees summary query error: ' . $e->getMessage());
        }

        $editPayment = null;
        $studentInfo = null;
        if (isset($_GET['edit'])) {
            try {
                $editPayment = $fees->find($_GET['edit']);
                if ($editPayment) {
                    $studentInfo = $students->find($editPayment['student_id']);
                }
            } catch (PDOException $e) {
                $editPayment = null;
                $studentInfo = null;
            }
        }

        $this->render('fees/index', [
            'page_title' => 'Fees Management',
            'fees_records' => $feesRecords,
            'summary' => $summary,
            'students_list' => $studentsList,
            'filter_student' => $filterStudent,
            'filter_term' => $filterTerm,
            'edit_payment' => $editPayment,
            'student_info' => $studentInfo,
        ]);
    }

    private function handlePost(FeeModel $fees): void
    {
        $action = $this->input('action');

        if ($action === 'record_payment') {
            $data = [
                'student_id' => $this->input('student_id'),
                'term' => trim($this->input('term', '')),
                'amount_paid' => $this->input('amount_paid'),
                'balance' => $this->input('balance', 0),
                'recorded_by' => Auth::user()['full_name'] ?? null,
            ];

            try {
                $fees->insert($data);
                $this->redirect('/dashboard/fees.php?success=' . rawurlencode('Payment recorded successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/fees.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'update_payment') {
            $id = $this->input('id');
            $data = [
                'term' => trim($this->input('term', '')),
                'amount_paid' => $this->input('amount_paid'),
                'balance' => $this->input('balance', 0),
            ];

            try {
                $fees->update($id, $data);
                $this->redirect('/dashboard/fees.php?success=' . rawurlencode('Payment updated successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/fees.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'delete_payment') {
            $id = $this->input('id');

            try {
                $fees->delete($id);
                $this->redirect('/dashboard/fees.php?success=' . rawurlencode('Payment record deleted successfully'));
            } catch (PDOException $e) {
                $this->redirect('/dashboard/fees.php?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        }
    }
}
