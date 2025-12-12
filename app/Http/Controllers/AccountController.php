<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Generate Donation Receipt PDF
     */
    public function donationReceipt(Donation $donation)
    {
        $donation->load(['receivedBy']);

        $pdf = Pdf::loadView('pdf.donation-receipt', [
            'donation' => $donation,
        ]);

        $pdf->setPaper('a5', 'portrait');

        return $pdf->stream("donation-receipt-{$donation->receipt_no}.pdf");
    }
}
