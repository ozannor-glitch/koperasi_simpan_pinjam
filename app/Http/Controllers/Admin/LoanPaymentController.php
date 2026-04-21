<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalItem;
use App\Models\LoanInstallment;
use App\Models\LoanInstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanPaymentController extends Controller
{
  public function pay($id)
{
    $installment = LoanInstallment::findOrFail($id);

    if ($installment->status == 'paid') {
        return back()->with('error','Sudah dibayar');
    }

    try {
  DB::transaction(function () use ($installment) {

    $installment->update([
        'status' => 'paid'
    ]);

    $journal = Journal::create([
        'date' => now(),
        'description' => 'Pembayaran angsuran ke-' . $installment->installment_number
    ]);

    $kas = Account::where('code','1-1001')->first();
    $piutang = Account::where('code','1-2001')->first();
    $bunga = Account::where('code','4001')->first();

    if (!$kas || !$piutang || !$bunga) {
        throw new \Exception('Akun tidak ditemukan, cek kode di database');
    }

    // 💰 Kas (Debit)
    JournalItem::create([
        'journal_id' => $journal->id,
        'account_id' => $kas->id,
        'debit' => $installment->amount_due,
        'credit' => 0
    ]);

    // 📉 Piutang (Credit)
    JournalItem::create([
        'journal_id' => $journal->id,
        'account_id' => $piutang->id,
        'debit' => 0,
        'credit' => $installment->principal
    ]);

    // 📈 Pendapatan Bunga (Credit)
    JournalItem::create([
        'journal_id' => $journal->id,
        'account_id' => $bunga->id,
        'debit' => 0,
        'credit' => $installment->interest
    ]);
});

        return back()->with('success','Pembayaran berhasil + jurnal masuk');

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
}
