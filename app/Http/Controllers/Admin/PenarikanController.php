<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\MemberSaving;
use App\Models\Penarikan;
use App\Models\SavingTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenarikanController extends Controller
{
     /* 📄 List data penarikan
     */
    public function index(Request $request)
    {

       $query = Penarikan::with(['user', 'memberSaving'])->latest();

    // 🔥 filter status
    $query->when($request->status && $request->status != 'all', function ($q) use ($request) {
        $q->where('status', $request->status);
    });

    // 🔥 filter bank
    $query->when($request->bank, function ($q) use ($request) {
        $q->where('bank', $request->bank);
    });

    // ✅ pakai query
    $data = $query->get();

    return view('superadmin.pages.penarikan.index', compact('data'));
    }

public function store(Request $request)
{
    return DB::transaction(function () use ($request) {

        $request->validate([
            'jumlah' => 'required',
            'bank' => 'required',
            'no_rekening' => 'required',
            'nama_rekening' => 'required'
        ]);

        $source = $request->source_id;

        if (!$source) {
            throw new \Exception('Pilih sumber dana dulu');
        }

        $jumlah = str_replace('.', '', $request->jumlah);

        $id = null;
        $sourceType = null;
        $userId = null;
        $memberSavingId = null;

        // ======================
        // 🔥 SAVING
        // ======================
        if (str_contains($source, 'saving_')) {

            $id = str_replace('saving_','',$source);
            $sourceType = 'saving';

            $saving = MemberSaving::findOrFail($id);

            if ($jumlah > $saving->balance) {
                throw new \Exception('Saldo tidak cukup');
            }

            $saving->balance -= $jumlah;
            $saving->save();

            $userId = $saving->user_id;
            $memberSavingId = $saving->id;
        }

        // ======================
        // 🔥 LOAN
        // ======================
        elseif (str_contains($source, 'loan_')) {

            $id = str_replace('loan_','',$source);
            $sourceType = 'loan';

            $loan = Loan::findOrFail($id);


            $current = $loan->withdrawable_amount > 0
            ? $loan->withdrawable_amount
            : $loan->total_amount;

            if ($jumlah > $current) {
                throw new \Exception('Limit pinjaman tidak cukup');
            }

            $userId = $loan->user_id;
        }

        // ======================
        // 🔥 SIMPAN
        // ======================
        Penarikan::create([
            'kode_penarikan' => 'WD-' . time(),
            'user_id' => $userId,
            'member_saving_id' => $memberSavingId,
            'source_type' => $sourceType,
            'source_id' => $id,
            'jumlah' => $jumlah,
            'jumlah_diterima' => $jumlah,
            'bank' => $request->bank,
            'no_rekening' => $request->no_rekening,
            'nama_rekening' => $request->nama_rekening,
            'status' => 'pending',
        ]);

        return redirect()->route('superadmin.penarikan.index')
            ->with('success','Penarikan berhasil disimpan');
    });
}

  public function create()
{
    $users = User::where('role','anggota')->get();

    $savings = MemberSaving::with('user','savingType')
        ->get()
        ->map(function ($s) {

            $pending = Penarikan::where('member_saving_id', $s->id)
                ->whereIn('status', ['pending','approved'])
                ->sum('jumlah');

            $s->available_balance = $s->balance - $pending;

            return $s;
        });

    // 🔥 PINDAH KE SINI (LUAR MAP)
    $loans = Loan::with('user')
        ->where('status','approved')
        ->get();

    return view('superadmin.pages.penarikan.create', compact('users','savings','loans'));
}
public function approve($id)
{
    DB::transaction(function () use ($id) {

        $penarikan = Penarikan::findOrFail($id);

        if ($penarikan->status != 'pending') {
            throw new \Exception('Sudah diproses');
        }

        // 🔥 HANDLE SUMBER
     if ($penarikan->source_type == 'saving') {

    $saving = MemberSaving::findOrFail($penarikan->source_id);

    if ($saving->balance < $penarikan->jumlah) {
        throw new \Exception('Saldo tidak cukup');
    }

    $saving->balance -= $penarikan->jumlah;
    $saving->save();
}

elseif ($penarikan->source_type == 'loan') {

    $loan = Loan::findOrFail($penarikan->source_id);

    $current = $loan->withdrawable_amount > 0
        ? $loan->withdrawable_amount
        : $loan->total_amount;

    if ($penarikan->jumlah > $current) {
        throw new \Exception('Limit pinjaman tidak cukup');
    }

    $loan->withdrawable_amount = $current - $penarikan->jumlah;
    $loan->save();
}

        // ✅ update status
        $penarikan->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);
    });

    return back()->with('success','Penarikan di-approve');
}
public function destroy($id)
{
    $penarikan = Penarikan::findOrFail($id);

    $penarikan->delete();

    return redirect()->back()->with('success', 'Data berhasil dihapus');
}
}
