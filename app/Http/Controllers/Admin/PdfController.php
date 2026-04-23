<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingTransaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function laporanPdf()
{
    $data = SavingTransaction::with('user')->get();

    $pdf = PDF::loadView('laporan.pdf', compact('data'));

    return $pdf->download('laporan.pdf');
}
}
