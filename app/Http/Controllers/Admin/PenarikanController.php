<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberSaving;
use App\Models\Penarikan;
use App\Models\SavingTransaction;
use App\Models\User;
use Illuminate\Http\Request;
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

   public function create()
{
    $users = User::where('role','anggota')->get();

    $savings = MemberSaving::with('user','savingType')
        ->get()
        ->map(function ($s) {

            // 🔥 hitung penarikan pending + approved
            $pending = Penarikan::where('member_saving_id', $s->id)
                ->whereIn('status', ['pending','approved'])
                ->sum('jumlah');

            // 🔥 saldo tersedia
            $s->available_balance = $s->balance - $pending;

            return $s;
        });

    return view('superadmin.pages.penarikan.create', compact('users','savings'));
}


public function store(Request $request)
{
    $request->validate([
        'member_saving_id' => 'required',
        'jumlah' => 'required|numeric|min:1000',
        'bank' => 'required',
        'no_rekening' => 'required',
        'nama_rekening' => 'required'
    ]);

    $saving = MemberSaving::with('savingType')->findOrFail($request->member_saving_id);

    // ❌ BLOCK SIMPANAN WAJIB
    if (strtolower($saving->savingType->name) == 'wajib') {
        return back()->with('error','Simpanan wajib tidak bisa ditarik');
    }

    // 🔥 HITUNG PENARIKAN YANG MASIH PROSES
    $pending = Penarikan::where('member_saving_id', $saving->id)
        ->whereIn('status', ['pending','approved'])
        ->sum('jumlah');

    // 🔥 SALDO TERSEDIA
    $available = $saving->balance - $pending;

    // ❌ VALIDASI
    if ($request->jumlah > $available) {
        return back()->with('error','Saldo tersedia tidak cukup');
    }

    // ✅ SIMPAN
    Penarikan::create([
        'kode_penarikan' => 'WD-'.rand(1000,9999),
        'user_id' => $saving->user_id,
        'member_saving_id' => $request->member_saving_id,
        'jumlah' => $request->jumlah,
        'jumlah_diterima' => $request->jumlah,
        'bank' => $request->bank,
        'no_rekening' => $request->no_rekening,
        'nama_rekening' => $request->nama_rekening,
        'status' => 'pending'
    ]);

    return redirect()->route('superadmin.penarikan.index')
        ->with('success','Penarikan berhasil dibuat');
}

    public function edit($id)
    {
        $penarikan = Penarikan::findOrFail($id);
        $savings = MemberSaving::with('user','savingType')->get();

        return view('superadmin.pages.penarikan.edit', compact('penarikan','savings'));
    }

    /**
     * 👁 Detail penarikan
     */
    public function show($id)
    {
        $penarikan = Penarikan::with(['user', 'memberSaving'])->findOrFail($id);

        return view('superadmin.pages.penarikan.show', compact('penarikan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1000',
            'bank' => 'required',
            'no_rekening' => 'required',
            'nama_rekening' => 'required'
        ]);

        $data = Penarikan::findOrFail($id);

        if ($data->status !== 'pending') {
            return back()->with('error','Tidak bisa edit, sudah diproses');
        }

        $saving = MemberSaving::findOrFail($data->member_saving_id);

        if ($saving->balance < $request->jumlah) {
            return back()->with('error','Saldo tidak cukup');
        }

        $data->update([
            'jumlah' => $request->jumlah,
            'jumlah_diterima' => $request->jumlah,
            'bank' => $request->bank,
            'no_rekening' => $request->no_rekening,
            'nama_rekening' => $request->nama_rekening
        ]);

        return redirect()->route('superadmin.penarikan.index')
            ->with('success','Penarikan berhasil diupdate');
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
        DB::transaction(function () use ($id) {

        $penarikan = Penarikan::lockForUpdate()->findOrFail($id);

        if ($penarikan->status !== 'approved') {
            throw new \Exception('Harus di-approve dulu');
        }

        $saving = MemberSaving::lockForUpdate()->findOrFail($penarikan->member_saving_id);

        if ($saving->balance < $penarikan->jumlah) {
            throw new \Exception('Saldo tidak cukup');
        }

        // 💸 potong saldo DI SINI
        $saving->balance -= $penarikan->jumlah;
        $saving->save();

        // 🧾 transaksi
        SavingTransaction::create([
            'user_id' => $penarikan->user_id,
            'saving_type_id' => $saving->saving_type_id,
            'transaction_type' => 'withdraw',
            'amount' => $penarikan->jumlah,
            'status' => 'success',
            'reference_id' => $penarikan->id,
            'reference_type' => 'penarikan'
        ]);

        $penarikan->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    });

    return back()->with('success','Penarikan selesai & saldo dipotong');
    }

    public function cancel($id)
{
    DB::transaction(function () use ($id) {

        $penarikan = Penarikan::lockForUpdate()->findOrFail($id);

        // hanya bisa cancel jika sudah approved / completed
        if (!in_array($penarikan->status, ['approved','completed'])) {
            throw new \Exception('Tidak bisa dibatalkan');
        }

        $saving = MemberSaving::lockForUpdate()->findOrFail($penarikan->member_saving_id);

        // 🔥 kembalikan saldo
        $saving->balance += $penarikan->jumlah;
        $saving->save();

        // update status
        $penarikan->status = 'cancelled';
        $penarikan->save();
    });

    return back()->with('success','Penarikan dibatalkan & saldo dikembalikan');
}

    public function destroy($id)
    {
        $data = Penarikan::findOrFail($id);

        if ($data->status !== 'pending') {
            return back()->with('error','Tidak bisa dihapus, sudah diproses');
        }

        $data->delete();

        return back()->with('success','Penarikan berhasil dihapus');
    }



}
