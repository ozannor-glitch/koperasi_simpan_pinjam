<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalItem;

class TrialBalanceController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('code')->get();

        $data = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {

            $items = JournalItem::where('account_id', $account->id)->get();

            $debit = $items->sum('debit');
            $credit = $items->sum('credit');

            // 🔥 SALDO AKHIR
            if (in_array($account->type, ['asset','expense'])) {
                $balance = $debit - $credit;
            } else {
                $balance = $credit - $debit;
            }

            // 🔥 MASUKKAN KE KOLOM
         if (in_array($account->type, ['asset','expense'])) {

        if ($balance >= 0) {
            $finalDebit = $balance;
            $finalCredit = 0;
        } else {
            $finalDebit = 0;
            $finalCredit = abs($balance); // 🔥 pindah ke credit
        }

    } else {

        if ($balance >= 0) {
            $finalDebit = 0;
            $finalCredit = $balance;
        } else {
            $finalDebit = abs($balance); // 🔥 pindah ke debit
            $finalCredit = 0;
        }

    }

            $totalDebit += $finalDebit;
            $totalCredit += $finalCredit;

            $data[] = [
                'account' => $account,
                'debit' => $finalDebit,
                'credit' => $finalCredit
            ];
        }

        return view('superadmin.pages.akuntansi.neraca_saldo.index', compact('data','totalDebit','totalCredit'));
    }
}
