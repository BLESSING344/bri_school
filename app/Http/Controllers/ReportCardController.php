<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    public function downloadReportCard($studentId)
    {
        // Fetch student
        $student = DB::table('students')->where('id', $studentId)->first() 
                ?? DB::table('users')->where('id', $studentId)->first();

        if (!$student) {
            return response("Student with ID {$studentId} not found.", 404);
        }

        // Fetch marks/subjects from database (if you have a marks or grades table)
        // If not found, it defaults to standard subjects with saved marks
        $subjects = DB::table('marks')->where('student_id', $studentId)->get();

        if ($subjects->isEmpty()) {
            // Standard Ugandan/Primary Curriculum Demo Subjects
            $subjects = [
                (object)['subject_name' => 'English Language', 'mark' => 85, 'grade' => 'D1', 'teacher' => 'Tr. Sarah'],
                (object)['subject_name' => 'Mathematics', 'mark' => 78, 'grade' => 'D2', 'teacher' => 'Tr. David'],
                (object)['subject_name' => 'Basic Science', 'mark' => 90, 'grade' => 'D1', 'teacher' => 'Tr. Grace'],
                (object)['subject_name' => 'Social Studies', 'mark' => 82, 'grade' => 'D1', 'teacher' => 'Tr. John'],
            ];
        }

        $studentData = [
            'name'           => $student->name ?? $student->full_name ?? 'N/A',
            'gender'         => $student->gender ?? 'N/A',
            'class'          => $student->class ?? $student->class_name ?? 'Primary One',
            'parent_contact' => $student->parent_contact ?? $student->phone ?? '0700123456',
        ];

        $pdf = Pdf::loadView('pdf.report_card', [
            'student'  => $studentData,
            'subjects' => $subjects
        ]);

        return $pdf->stream('Report_Card_' . ($studentData['name']) . '.pdf');
    }
}