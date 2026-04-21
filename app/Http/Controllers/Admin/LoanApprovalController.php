<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\JournalService;
use App\Models\Account;

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

    $loan = Loan::findOrFail($loanId);

    $loan->update([
        'status' => 'approved'
    ]);

    // 🔥 AMBIL ACCOUNT (JANGAN HARDCODE)
    $kas = Account::where('name', 'Kas')->first();
    $piutang = Account::where('name', 'Piutang Pinjaman')->first();

    if (!$kas || !$piutang) {
        throw new \Exception('Account Kas / Piutang belum ada!');
    }

    // 🔥 AUTO JURNAL
    JournalService::create(
        now(),
        'Pencairan pinjaman #' . $loan->id,
        [
            [
                'account_id' => $piutang->id,
                'debit' => $loan->total_amount,
                'credit' => 0
            ],
            [
                'account_id' => $kas->id,
                'debit' => 0,
                'credit' => $loan->total_amount
            ]
        ],
        'loan',
        $loan->id
    );

    // generate cicilan
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
