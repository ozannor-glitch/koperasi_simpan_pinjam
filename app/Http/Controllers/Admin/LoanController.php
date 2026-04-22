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
        $users = User::where('role', 'anggota')
            ->select('id', 'name')
            ->get();
        $loanTypes = LoanType::all();
        return view('superadmin.pages.pinjaman.create', compact('loanTypes', 'users'));
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

                $levels = config('loan.approval_levels', [1, 2, 3]);

                foreach ($levels as $level) {
                    LoanApproval::create([
                        'loan_id' => $loan->id,
                        'level' => $level,
                        'status' => 'pending'
                    ]);
                }
            });

            return redirect()->route('superadmin.pages.pinjaman.index')
                ->with('success', 'Pengajuan berhasil');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        $loan = Loan::with('installments', 'loanType')->findOrFail($id);
        return view('superadmin.pages.pinjaman.show', compact('loan'));
    }

    public function generateInstallments($loan)
    {
        // contoh sederhana dulu
        for ($i = 1; $i <= $loan->tenor; $i++) {
            \App\Models\LoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'amount' => $loan->total_amount / $loan->tenor,
                'due_date' => now()->addMonths($i),
                'status' => 'unpaid'
            ]);
        }
    }
}
