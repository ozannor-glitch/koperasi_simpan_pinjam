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
    // 🔥 BERSIHKAN INPUT ANGKA
    $collateral = preg_replace('/\D/', '', $request->collateral_value ?? '');
    $amount = preg_replace('/\D/', '', $request->total_amount ?? '');

    $request->merge([
        'collateral_value' => (int) $collateral,
        'total_amount' => (int) $amount,
    ]);

    // 🔥 VALIDASI
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'loan_type_id' => 'required|exists:loan_types,id',
        'total_amount' => 'required|numeric|min:1',
        'tenor' => 'required|integer|min:1',

        'collateral_name' => 'required',
        'collateral_value' => 'required|numeric|min:1',
        'collateral_photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
    ]);

    $type = LoanType::findOrFail($request->loan_type_id);

    $jaminan  = (int) $request->collateral_value;
    $pinjaman = (int) $request->total_amount;

    // 🔥 BATAS JAMINAN
    $ratio = (int) ($type->collateral_ratio ?? 70);
    $maxByCollateral = intdiv($jaminan * $ratio, 100);

    // 🔥 BATAS PRODUK
    $maxPlafon = (int) $type->max_plafon;

    // 🔥 FINAL LIMIT (AMBIL YANG TERKECIL)
    $finalMax = min($maxByCollateral, $maxPlafon);

    if ($pinjaman > $finalMax) {
        return back()->withInput()->with('error',
            "Max pinjaman: Rp " . number_format($finalMax)
        );
    }

    // 🔥 SIMPAN DATA
    $loan = Loan::create([
        'user_id' => $request->user_id,
        'loan_type_id' => $request->loan_type_id,
        'total_amount' => $pinjaman,
        'tenor' => $request->tenor,
        'collateral_name' => $request->collateral_name,
        'collateral_value' => $jaminan,
        'status' => 'pending'
    ]);

    // 🔥 UPLOAD FOTO
    if ($request->hasFile('collateral_photo')) {
        $path = $request->file('collateral_photo')->store('collateral', 'public');
        $loan->collateral_photo = $path;
        $loan->save();
    }

    return redirect()->route('superadmin.pinjaman.index')
        ->with('success', 'Pengajuan berhasil');
}
    public function show($id)
{
    $loan = Loan::with('installments','loanType','user')->findOrFail($id);

    // ✅ HARUS DI LUAR if
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

    if ($request->status == 'rejected') {
        $loan->rejection_reason = $request->rejection_reason;
    }

    $loan->save();

    // 🔥 AUTO GENERATE CICILAN
    if ($request->status == 'approved') {

        // ❗ jangan dobel generate
        if ($loan->installments()->count() == 0) {
            $this->generateInstallments($loan); // ✅ FIX
        }
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
