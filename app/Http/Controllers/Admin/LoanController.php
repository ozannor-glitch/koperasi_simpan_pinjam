<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApproval;
use App\Models\LoanType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with('loanType')->latest()->get();
        return view('superadmin.pages.pinjaman.index', compact('loans'));
    }

    public function create()
    {
        $users = User::where('role','anggota')
            ->select('id','name')
            ->get();
        $loanTypes = LoanType::all();
        return view('superadmin.pages.pinjaman.create', compact('loanTypes','users'));
    }

    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'loan_type_id' => 'required|exists:loan_types,id',
        'total_amount' => 'required|numeric|min:1',
        'tenor' => 'required|integer|min:1'
    ]);

    $type = LoanType::findOrFail($request->loan_type_id);

    if ($request->total_amount > $type->max_plafon) {
        return back()->with('error', 'Melebihi plafon');
    }

    if ($request->tenor > $type->max_tenor_months) {
        return back()->with('error', 'Tenor terlalu panjang');
    }

    try {
        DB::transaction(function () use ($request, $type) {

            $loan = Loan::create([
                'user_id' => $request->user_id, // 🔥 FIX
                'loan_type_id' => $request->loan_type_id,
                'total_amount' => $request->total_amount,
                'tenor' => $request->tenor,
                'status' => 'pending'
            ]);

            $levels = config('loan.approval_levels', [1,2,3]);

            foreach ($levels as $level) {
                LoanApproval::create([
                    'loan_id' => $loan->id,
                    'level' => $level,
                    'status' => 'pending'
                ]);
            }
        });

        return redirect()->route('superadmin.pages.pinjaman.index')
            ->with('success','Pengajuan berhasil');

    } catch (\Exception $e) {
        return back()->with('error', 'Gagal: '.$e->getMessage());
    }
}
    public function show($id)
    {
        $loan = Loan::with('installments','loanType')->findOrFail($id);
        return view('superadmin.pages.pinjaman.show', compact('loan'));
    }

public function generateInstallments($loan)
{
    $P = $loan->total_amount;
    $n = $loan->tenor;

    $type = $loan->loanType;
    $r = $type->interest_rate_percent / 100;

    $method = $type->interest_method ?? 'flat'; // flat | efektif | anuitas

    $remaining = $P;

    for ($i = 1; $i <= $n; $i++) {

        if ($method == 'flat') {

            $principal = $P / $n;
            $interest = $P * $r;
            $total = $principal + $interest;

        } elseif ($method == 'efektif') {

            $principal = $P / $n;
            $interest = $remaining * $r;
            $total = $principal + $interest;

        } elseif ($method == 'anuitas') {

            $A = $P * ($r * pow(1+$r, $n)) / (pow(1+$r, $n) - 1);

            $interest = $remaining * $r;
            $principal = $A - $interest;
            $total = $A;

        }

        $remaining -= $principal;

        \App\Models\LoanInstallment::create([
            'loan_id' => $loan->id,
            'installment_number' => $i,
            'principal' => $principal,
            'interest' => $interest,
            'amount_due' => $total,
            'remaining_balance' => max($remaining, 0),
            'due_date' => now()->addMonths($i),
            'status' => 'unpaid'
        ]);
    }
}
public function updateStatus(Request $request, $id)
{
    $loan = Loan::with('loanType')->findOrFail($id);

    $loan->status = $request->status;
    $loan->save();

     // 🔥 TAMBAHKAN INI
        if ($request->status == 'approved') {
            $loan->generateInstallments();
        }

    return back()->with('success','Status diperbarui');
}
public function uploadAkad(Request $request, $id)
{
    $request->validate([
        'akad_file' => 'required|file|mimes:pdf,jpg,png|max:2048'
    ]);

    $loan = Loan::findOrFail($id);

    $file = $request->file('akad_file')->store('akad', 'public');

    $loan->akad_file = $file;
    $loan->save();

    return back()->with('success','Dokumen berhasil diupload');
}
}
