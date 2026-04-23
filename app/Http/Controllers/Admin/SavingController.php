<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberSaving;
use App\Models\SavingTransaction;
use App\Models\SavingType;
use App\Models\User;
use Illuminate\Http\Request;

class SavingController extends Controller
{
    public function index(Request $request)
    {
       $users = User::where('role', 'anggota')->get();
    $savingTypes = SavingType::all();

    $query = MemberSaving::with('user','savingType');

    // 🔥 FILTER SALDO
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    $savings = $query->latest()->paginate(5);

    $totalSaldo = (clone $query)->sum('balance');

    // 🔥 FILTER HISTORI
    $trxQuery = SavingTransaction::with('user','savingType');

    if ($request->filled('user_id')) {
        $trxQuery->where('user_id', $request->user_id);
    }

    $transactions = $trxQuery->latest()->get();

    return view('superadmin.pages.saving.index', compact(
        'users','savingTypes','savings','transactions','totalSaldo'
    ));
    }
public function create()
{
    $users = User::where('role','anggota')->get();
    $savingTypes = SavingType::all();

    return view('superadmin.pages.saving.create', compact('users','savingTypes'));
}


    // 🔥 SETOR
    public function store(Request $request)
    {
        $request->merge([
            'amount' => (int) str_replace('.', '', $request->amount)
        ]);
        $request->validate([
            'user_id' => 'required',
            'saving_type_id' => 'required',
            'amount' => 'required|numeric|min:1000'
        ]);

        $userId = $request->input('user_id');
        $typeId = $request->input('saving_type_id');
        $amount = $request->input('amount');

        $saving = MemberSaving::firstOrCreate([
            'user_id' => $userId,
            'saving_type_id' => $typeId
        ]);

            SavingTransaction::create([
            'user_id' => $request->user_id,
            'saving_type_id' => $request->saving_type_id,
            'transaction_type' => 'setor',
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Setoran berhasil');
    }

    //Tarik Uang
public function withdraw(Request $request)
{
    $request->validate([
        'user_id' => 'required',
        'saving_type_id' => 'required',
        'amount' => 'required|numeric|min:1'
    ]);

    $userId = $request->user_id;
    $typeId = $request->saving_type_id;
    $amount = (int) $request->amount;

    $saving = MemberSaving::where([
        'user_id' => $userId,
        'saving_type_id' => $typeId
    ])->first();

    if (!$saving) {
        return back()->withErrors('Data tidak ditemukan');
    }

      $savingType = $saving->savingType;

    // 🔥 SIMPANAN WAJIB TIDAK BOLEH DITARIK
    if ($savingType->name === 'Wajib') {
        return back()->withErrors('Simpanan wajib tidak dapat ditarik');
    }

    // 🔥 MINIMAL PENARIKAN
    $minimumTarik = $saving->savingType->minimum_withdraw;

    if ($amount < $minimumTarik) {
        return back()->withErrors('Minimal penarikan Rp ' . number_format($minimumTarik));
    }

    // 🔥 SALDO TIDAK CUKUP
    if ($amount > $saving->balance) {
        return back()->withErrors('Saldo tidak cukup');
    }

    // 🔥 KURANGI SALDO
    $saving->balance -= $amount;
    $saving->save();

    return back()->with('success', 'Berhasil tarik saldo');
}
    // Persetujuan Penarikan
    public function approve($id)
    {
    $trx = SavingTransaction::findOrFail($id);

    if ($trx->status !== 'pending') {
    return back()->withErrors('Transaksi sudah diproses');

    }

    $saving = MemberSaving::firstOrCreate([
        'user_id' => $trx->user_id,
        'saving_type_id' => $trx->saving_type_id
    ]);
if ($trx->transaction_type == 'setor') {

    $saving->balance += $trx->amount;

} elseif ($trx->transaction_type == 'tarik') {

    if ($saving->balance < $trx->amount) {
        return back()->withErrors('Saldo tidak cukup');
    }

    $saving->balance -= $trx->amount;
}

$saving->save();

$trx->status = 'approved';
$trx->save();

return back()->with('success','Transaksi disetujui');
}

// Penolakan Penarikan
public function reject($id)
{
    $trx = SavingTransaction::findOrFail($id);
    $trx->status = 'rejected';
    $trx->save();

    return back()->with('success','Transaksi ditolak');
}

    //Histori Transaksi
    public function transactions()
    {
        $transactions = SavingTransaction::with('user','savingType')->latest()->get();

        return view('superadmin.pages.saving.transactions', compact('transactions'));
    }

    //Delete Transaksi
    public function destroy($id)
    {
    $trx = SavingTransaction::findOrFail($id);
    $trx->delete();

    return back()->with('success','Transaksi dihapus');
    }

    //Fungsi Bunga
    public function generateBunga()
    {
    $persen = 2; // 2%

    $savings = MemberSaving::all();

    foreach ($savings as $saving) {

        $bunga = ($saving->balance * $persen) / 100;

        SavingTransaction::create([
            'user_id' => $saving->user_id,
            'saving_type_id' => $saving->saving_type_id,
            'transaction_type' => 'bunga',
            'amount' => $bunga,
            'status' => 'approved'
        ]);

        $saving->balance += $bunga;
        $saving->save();
    }

    return back()->with('success','Bunga berhasil dibagikan');
    }
}
