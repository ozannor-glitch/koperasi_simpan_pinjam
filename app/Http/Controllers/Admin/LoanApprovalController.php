<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanApprovalController extends Controller
{
    public function index()
    {
        $approvals = LoanApproval::with('loan')->where('status','pending')->get();
        return view('approvals.index', compact('approvals'));
    }

    public function approve($loanId)
    {
        $approval = LoanApproval::where('loan_id', $loanId)
            ->where('status', 'pending')
            ->orderBy('level')
            ->first();

        $approval->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        $next = LoanApproval::where('loan_id', $loanId)
            ->where('status', 'pending')
            ->first();

        if (!$next) {
            $loan = Loan::find($loanId);
            $loan->update(['status' => 'approved']);

            app(LoanController::class)->generateInstallments($loan);
        }

        return back()->with('success','Approved');
    }

    public function reject($loanId)
    {
        LoanApproval::where('loan_id',$loanId)
            ->where('status','pending')
            ->first()
            ->update([
                'status' => 'rejected',
                'approved_by' => Auth::id()
            ]);

        Loan::where('id',$loanId)->update(['status'=>'rejected']);

        return back()->with('error','Rejected');
    }
}
