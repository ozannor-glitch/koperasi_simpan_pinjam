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
    public function index()
    {
      $users = User::where('role', 'anggota')->get();

    $savingTypes = SavingType::all(); // 🔥 penting

    $savings = MemberSaving::with('user','savingType')
        ->latest()
        ->paginate(5);

    $transactions = SavingTransaction::with('user','savingType')->latest()->get();

    return view('superadmin.pages.saving.index', compact(
        'users',
        'savingTypes',
        'savings',
        'transactions'
    ));
    }


    // 🔥 SETOR
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'saving_type_id' => 'required',
            'amount' => 'required|numeric|min:1000',
        ]);

        $userId = $request->input('user_id');
        $typeId = $request->input('saving_type_id');
        $amount = $request->input('amount');

        $saving = MemberSaving::firstOrCreate([
            'user_id' => $userId,
            'saving_type_id' => $typeId
        ]);

        $saving->balance += $amount;
        $saving->save();

        SavingTransaction::create([
            'user_id' => $userId,
            'saving_type_id' => $typeId,
            'transaction_type' => 'setor',
            'amount' => $amount,
        ]);

        return back()->with('success', 'Setoran berhasil');
    }

    //Tarik Uang
    public function withdraw(Request $request)
{
    $userId = $request->input('user_id');
    $typeId = $request->input('saving_type_id');
    $amount = $request->input('amount');

    $saving = MemberSaving::where([
        'user_id' => $userId,
        'saving_type_id' => $typeId
    ])->first();

    if (!$saving || $saving->balance < $amount) {
        return back()->withErrors('Saldo tidak cukup');
    }

    $saving->balance -= $amount;
    $saving->save();

    SavingTransaction::create([
        'user_id' => $userId,
        'saving_type_id' => $typeId,
        'transaction_type' => 'tarik',
        'amount' => $amount,
    ]);

    return back()->with('success','Penarikan berhasil');
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
}
