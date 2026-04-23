<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

class Slip extends Controller
{
    public function slip($id)
{
    $loan = Loan::with('user')->findOrFail($id);

    $pdf = FacadePdf::loadView('laporan.slip', compact('loan'));

    return $pdf->stream('slip.pdf');
}
}
