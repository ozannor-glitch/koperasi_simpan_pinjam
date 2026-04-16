<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanInstallment;
use App\Models\LoanInstallmentPayment;
use Illuminate\Http\Request;

class LoanPaymentController extends Controller
{
    public function pay(Request $request, $id)
    {
        $installment = LoanInstallment::findOrFail($id);

        $penalty = 0;

        if (now()->gt($installment->due_date)) {
            $daysLate = now()->diffInDays($installment->due_date);
            $penalty = $daysLate * 0.001 * $installment->amount_due;
        }

        LoanInstallmentPayment::create([
            'loan_installment_id' => $id,
            'amount_paid' => $installment->amount_due,
            'penalty' => $penalty,
            'paid_at' => now()
        ]);

        $installment->update(['status'=>'paid']);

        return back()->with('success','Pembayaran berhasil');
    }

}
