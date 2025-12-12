<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Generate Student ID Card PDF
     */
    public function idCard(Student $student)
    {
        $student->load(['class', 'section', 'academicYear']);

        $pdf = Pdf::loadView('pdf.student-id-card', [
            'student' => $student,
        ]);

        $pdf->setPaper([0, 0, 243, 153], 'landscape'); // CR-80 card size in points

        return $pdf->stream("student-id-{$student->admission_no}.pdf");
    }

    /**
     * Generate Transfer Certificate PDF
     */
    public function transferCertificate(Student $student)
    {
        $student->load(['class', 'section', 'academicYear', 'enrollments.class', 'enrollments.academicYear']);

        $pdf = Pdf::loadView('pdf.student-tc', [
            'student' => $student,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("tc-{$student->admission_no}.pdf");
    }

    /**
     * Export students to Excel
     */
    public function export(Request $request)
    {
        // Placeholder for Excel export - requires maatwebsite/excel implementation
        return redirect()->back()->with('info', 'Export feature coming soon');
    }
}
