<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Generate Teacher ID Card PDF
     */
    public function idCard(Teacher $teacher)
    {
        $teacher->load(['department', 'designation']);

        $pdf = Pdf::loadView('pdf.teacher-id-card', [
            'teacher' => $teacher,
        ]);

        $pdf->setPaper([0, 0, 243, 153], 'landscape'); // CR-80 card size

        return $pdf->stream("teacher-id-{$teacher->employee_id}.pdf");
    }

    /**
     * Export teachers to Excel
     */
    public function export(Request $request)
    {
        // Placeholder for Excel export
        return redirect()->back()->with('info', 'Export feature coming soon');
    }
}
