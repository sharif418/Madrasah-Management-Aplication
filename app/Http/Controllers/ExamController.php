<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use App\Models\Grade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Generate Tabulation Sheet PDF
     */
    public function tabulation(Exam $exam)
    {
        $exam->load(['class', 'examType', 'academicYear', 'schedules.subject']);

        $subjects = $exam->schedules->pluck('subject');

        // Get all marks for this exam
        $marks = Mark::where('exam_id', $exam->id)
            ->with(['student', 'subject', 'grade'])
            ->get()
            ->groupBy('student_id');

        $studentResults = [];

        foreach ($marks as $studentId => $studentMarks) {
            $student = $studentMarks->first()->student;
            $totalObtained = 0;
            $totalFull = 0;
            $subjectMarks = [];
            $allPassed = true;

            foreach ($studentMarks as $mark) {
                $totalObtained += $mark->marks_obtained;
                $totalFull += $mark->full_marks;
                $subjectMarks[$mark->subject_id] = [
                    'marks' => $mark->marks_obtained,
                    'full' => $mark->full_marks,
                    'passed' => $mark->is_passed,
                    'grade' => $mark->grade?->name,
                ];

                if (!$mark->is_passed) {
                    $allPassed = false;
                }
            }

            $percentage = $totalFull > 0 ? ($totalObtained / $totalFull) * 100 : 0;
            $grade = Grade::getGradeForMarks($percentage);

            $studentResults[$studentId] = [
                'student' => $student,
                'subjects' => $subjectMarks,
                'total_obtained' => $totalObtained,
                'total_full' => $totalFull,
                'percentage' => round($percentage, 2),
                'grade' => $grade?->name ?? '-',
                'gpa' => $grade?->grade_point ?? 0,
                'is_passed' => $allPassed,
            ];
        }

        // Sort by percentage descending
        uasort($studentResults, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        // Assign positions
        $position = 1;
        foreach ($studentResults as &$data) {
            $data['position'] = $position++;
        }

        $pdf = Pdf::loadView('pdf.exam-tabulation', [
            'exam' => $exam,
            'subjects' => $subjects,
            'studentResults' => $studentResults,
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream("tabulation-{$exam->id}.pdf");
    }

    /**
     * Generate Individual Marksheet PDF
     */
    public function marksheet(Exam $exam, Student $student)
    {
        $exam->load(['class', 'examType', 'academicYear', 'schedules.subject']);
        $student->load(['class', 'section']);

        $marks = Mark::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->with(['subject', 'grade'])
            ->get();

        $totalObtained = $marks->sum('marks_obtained');
        $totalFull = $marks->sum('full_marks');
        $percentage = $totalFull > 0 ? ($totalObtained / $totalFull) * 100 : 0;
        $grade = Grade::getGradeForMarks($percentage);

        $pdf = Pdf::loadView('pdf.marksheet', [
            'exam' => $exam,
            'student' => $student,
            'marks' => $marks,
            'totalObtained' => $totalObtained,
            'totalFull' => $totalFull,
            'percentage' => round($percentage, 2),
            'grade' => $grade,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("marksheet-{$student->admission_no}.pdf");
    }
}
