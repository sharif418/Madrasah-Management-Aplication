<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Generate Fee Receipt PDF
     */
    public function receipt(FeePayment $payment)
    {
        $payment->load(['student.class', 'student.section', 'studentFee.feeStructure.feeType', 'receivedBy']);

        $pdf = Pdf::loadView('pdf.fee-receipt', [
            'payment' => $payment,
        ]);

        $pdf->setPaper('a5', 'portrait');

        return $pdf->stream("receipt-{$payment->receipt_no}.pdf");
    }

    /**
     * Due Report - Students with pending fees
     */
    public function dueReport(Request $request)
    {
        $query = \App\Models\StudentFee::whereIn('status', ['pending', 'partial', 'overdue'])
            ->with(['student.class', 'student.section', 'feeStructure.feeType']);

        if ($request->class_id) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->academic_year_id) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $dues = $query->orderByDesc('due_amount')->get();

        $pdf = Pdf::loadView('pdf.fee-due-report', [
            'dues' => $dues,
            'totalDue' => $dues->sum('due_amount'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("due-report-" . now()->format('Y-m-d') . ".pdf");
    }

    /**
     * Collection Report - Fee collections for a date range
     */
    public function collectionReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now();

        $payments = FeePayment::whereBetween('payment_date', [$startDate, $endDate])
            ->with(['student', 'studentFee.feeStructure.feeType', 'receivedBy'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.fee-collection-report', [
            'payments' => $payments,
            'totalCollection' => $payments->sum('amount'),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("collection-report.pdf");
    }
}
