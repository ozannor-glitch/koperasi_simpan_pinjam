<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Http\Request;

class LoanInstallmentController extends Controller
{
    public function index($loanId)
    {
        $loan = Loan::with('installments')->findOrFail($loanId);
        return view('installments.index', compact('loan'));
    }

    public function show($id)
    {
        $installment = LoanInstallment::findOrFail($id);
        return view('installments.show', compact('installment'));
    }
  
}
