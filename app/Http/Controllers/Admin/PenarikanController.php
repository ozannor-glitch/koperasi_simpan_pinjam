<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberSaving;
use App\Models\Penarikan;
use App\Models\SavingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenarikanController extends Controller
{
     /* 📄 List data penarikan
     */
    public function index(Request $request)
    {
        $query = Penarikan::with(['user', 'memberSaving'])->latest();

        //filter status
       $query->when($request->status && $request->status != 'all', function ($q) use ($request) {
            $q->where('status', $request->status);
        });
        //filter status
        $query->when($request->bank, function ($q) use ($request) {
            $q->where('bank', $request->bank);
        });
                $data = Penarikan::with(['user', 'memberSaving'])
            ->latest()
            ->get();

        return view('superadmin.pages.penarikan.index', compact('data'));
    }

    /**
     * 👁 Detail penarikan
     */
    public function show($id)
    {
        $penarikan = Penarikan::with(['user', 'memberSaving'])->findOrFail($id);

        return view('superadmin.pages.penarikan.show', compact('penarikan'));
    }

    /**
     * ✅ APPROVE PENARIKAN
     */
    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $penarikan = Penarikan::lockForUpdate()->findOrFail($id);

            // ❌ Cegah double approve
            if ($penarikan->status !== 'pending') {
                throw new \Exception('Penarikan sudah diproses');
            }

            $saving = MemberSaving::lockForUpdate()->findOrFail($penarikan->member_saving_id);

            // ❌ Validasi saldo
            if ($saving->balance < $penarikan->jumlah) {
                throw new \Exception('Saldo tidak cukup');
            }

            // 💸 Kurangi saldo
            $saving->balance -= $penarikan->jumlah;
            $saving->save();

            // 🧾 Catat transaksi
            SavingTransaction::create([
                'user_id' => $penarikan->user_id,
                'saving_type_id' => $saving->saving_type_id,
                'transaction_type' => 'withdraw',
                'amount' => $penarikan->jumlah,
                'status' => 'success',
                'reference_id' => $penarikan->id,
                'reference_type' => 'penarikan'
            ]);

            // ✅ Update status
            $penarikan->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Penarikan berhasil di-approve');
    }

    /**
     * ❌ REJECT PENARIKAN
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string'
        ]);

        $penarikan = Penarikan::findOrFail($id);

        if ($penarikan->status !== 'pending') {
            return back()->with('error', 'Sudah diproses');
        }

        $penarikan->update([
            'status' => 'rejected',
            'catatan' => $request->catatan
        ]);

        return redirect()->back()->with('success', 'Penarikan ditolak');
    }

    /**
     * 💰 COMPLETE (sudah ditransfer)
     */
    public function complete($id)
    {
        $penarikan = Penarikan::findOrFail($id);

        if ($penarikan->status !== 'approved') {
            return back()->with('error', 'Harus di-approve dulu');
        }

        $penarikan->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return back()->with('success', 'Penarikan selesai');
    }

}
